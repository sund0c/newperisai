<?php
// app/Http/Controllers/Auth/TwoFactorController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use App\Models\AuditLog;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    // Tampilkan form setup 2FA
    public function showSetup()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['google2fa_secret' => encrypt($secret)]);
        }

        $secret = decrypt($user->google2fa_secret);
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate QR code sebagai SVG di server
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.2fa-setup', compact('qrCodeSvg', 'secret'));
    }

    // Aktifkan 2FA setelah verifikasi OTP
    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        $valid = $google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid. Coba lagi.']);
        }

        $user->update(['google2fa_enabled' => true]);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => '2fa_enabled',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard')->with('success', '2FA berhasil diaktifkan!');
    }

    // Tampilkan form verifikasi OTP saat login
    public function showVerify()
    {
        if (!Auth::check()) return redirect()->route('login');
        if (session('2fa_verified')) return redirect()->route('dashboard');

        return view('auth.2fa-verify');
    }

    // Proses verifikasi OTP saat login
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        $valid = $google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => '2fa_failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return back()->withErrors(['otp' => 'Kode OTP salah. Periksa Google Authenticator Anda.']);
        }

        $request->session()->put('2fa_verified', true);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'login_2fa_success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    // Nonaktifkan 2FA
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'otp'      => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = decrypt($user->google2fa_secret);

        if (!$google2fa->verifyKey($secret, $request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        $user->update([
            'google2fa_enabled' => false,
            'google2fa_secret'  => null,
        ]);

        session()->forget('2fa_verified');

        return back()->with('success', '2FA telah dinonaktifkan.');
    }
}
