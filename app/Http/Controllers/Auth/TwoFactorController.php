<?php
// app/Http/Controllers/Auth/TwoFactorController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use App\Models\AuditLog;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    // Maksimum percobaan OTP sebelum lockout
    private const MAX_ATTEMPTS = 5;

    // Durasi lockout dalam detik (5 menit)
    private const LOCKOUT_SECONDS = 300;

    // Window TOTP: berapa banyak periode 30-detik yang ditoleransi
    // 1 = toleransi ±30 detik dari waktu sekarang (cukup untuk clock skew normal)
    private const TOTP_WINDOW = 1;

    // =========================================================
    // SETUP — Tampilkan form setup 2FA
    // =========================================================
    public function showSetup()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // Generate secret baru hanya jika belum ada
        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey(32); // 32 chars = 160-bit entropy
            $user->update(['google2fa_secret' => encrypt($secret)]);
        }

        $secret = decrypt($user->google2fa_secret);

        // Bangun otpauth:// URI secara manual — tidak ada external call
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Render QR code sebagai SVG murni di server — tidak butuh internet
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = base64_encode($writer->writeString($qrCodeUrl));

        // Secret dikirim ke view hanya untuk tampilan manual entry
        // Tidak di-log, tidak di-cache
        return view('auth.2fa-setup', compact('qrCodeSvg', 'secret'));
    }

    // =========================================================
    // ENABLE — Aktifkan 2FA setelah verifikasi OTP pertama
    // =========================================================
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        // Rate limiting untuk endpoint enable (cegah brute-force saat setup)
        $rateLimiterKey = '2fa_enable:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimiterKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            return back()->withErrors([
                'otp' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (!$user->google2fa_secret) {
            return back()->withErrors(['otp' => 'Secret tidak ditemukan. Muat ulang halaman.']);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        // verifyKeyNewer: hanya terima OTP yang LEBIH BARU dari timestamp terakhir
        // Ini mencegah replay attack — OTP yang sama tidak bisa dipakai dua kali
        $lastTimestamp = Cache::get('2fa_last_ts:' . $user->id, null);

        $currentTimestamp = $google2fa->verifyKeyNewer(
            $secret,
            $request->otp,
            $lastTimestamp,
            self::TOTP_WINDOW
        );

        if ($currentTimestamp === false) {
            RateLimiter::hit($rateLimiterKey, self::LOCKOUT_SECONDS);

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => '2fa_enable_failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors(['otp' => 'Kode OTP tidak valid. Coba lagi.']);
        }

        // Simpan timestamp OTP terakhir yang valid — untuk replay prevention
        // TTL: 2 menit (lebih dari satu window TOTP, cukup untuk mencegah replay)
        Cache::put('2fa_last_ts:' . $user->id, $currentTimestamp, now()->addMinutes(2));

        // Reset rate limiter setelah berhasil
        RateLimiter::clear($rateLimiterKey);

        $user->update(['google2fa_enabled' => true]);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => '2fa_enabled',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard')->with('success', '2FA berhasil diaktifkan!');
    }

    // =========================================================
    // SHOW VERIFY — Tampilkan form verifikasi OTP saat login
    // =========================================================
    public function showVerify()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (session('2fa_verified')) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();

        // Edge case: user punya flag enabled tapi secret null (misal: migrasi data)
        if ($user->google2fa_enabled && !$user->google2fa_secret) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Konfigurasi 2FA bermasalah. Hubungi administrator.']);
        }

        return view('auth.2fa-verify');
    }

    // =========================================================
    // VERIFY — Proses verifikasi OTP saat login
    // =========================================================
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        // Rate limiting per user — cegah brute-force 6-digit (hanya 1 juta kombinasi)
        $rateLimiterKey = '2fa_verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimiterKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => '2fa_rate_limited',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'otp' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (!$user->google2fa_secret) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Konfigurasi 2FA bermasalah. Hubungi administrator.']);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        // Replay prevention: ambil timestamp OTP terakhir yang dipakai user ini
        $lastTimestamp = Cache::get('2fa_last_ts:' . $user->id, null);

        $currentTimestamp = $google2fa->verifyKeyNewer(
            $secret,
            $request->otp,
            $lastTimestamp,
            self::TOTP_WINDOW
        );

        if ($currentTimestamp === false) {
            RateLimiter::hit($rateLimiterKey, self::LOCKOUT_SECONDS);

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => '2fa_failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors(['otp' => 'Kode OTP salah. Periksa aplikasi authenticator Anda.']);
        }

        // Simpan timestamp — OTP ini tidak bisa dipakai lagi di window yang sama
        Cache::put('2fa_last_ts:' . $user->id, $currentTimestamp, now()->addMinutes(2));

        // Reset rate limiter setelah berhasil
        RateLimiter::clear($rateLimiterKey);

        // Tandai session bahwa 2FA sudah diverifikasi
        $request->session()->put('2fa_verified', true);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'login_2fa_success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    // =========================================================
    // DISABLE — Nonaktifkan 2FA (butuh password + OTP valid)
    // =========================================================
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'otp'      => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        // Rate limiting untuk disable juga — cegah brute-force
        $rateLimiterKey = '2fa_disable:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimiterKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            return back()->withErrors([
                'otp' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (!$user->google2fa_secret) {
            return back()->withErrors(['otp' => '2FA tidak aktif.']);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        $lastTimestamp = Cache::get('2fa_last_ts:' . $user->id, null);

        $currentTimestamp = $google2fa->verifyKeyNewer(
            $secret,
            $request->otp,
            $lastTimestamp,
            self::TOTP_WINDOW
        );

        if ($currentTimestamp === false) {
            RateLimiter::hit($rateLimiterKey, self::LOCKOUT_SECONDS);
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        RateLimiter::clear($rateLimiterKey);

        // Hapus semua data 2FA user
        $user->update([
            'google2fa_enabled' => false,
            'google2fa_secret'  => null,
        ]);

        // Hapus cache timestamp
        Cache::forget('2fa_last_ts:' . $user->id);

        // Hapus flag session
        session()->forget('2fa_verified');

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => '2fa_disabled',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', '2FA telah dinonaktifkan.');
    }
}
