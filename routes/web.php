<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// =====================
// PUBLIC ROUTES
// =====================
Route::get('/', fn() => redirect()->route('login'));

// =====================
// EMAIL VERIFICATION — Custom route menggantikan Fortify's default
// Handles kasus user klik link di browser berbeda (tanpa session)
// =====================
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    // Jika belum login, coba auto-login berdasarkan id & hash
    if (!Auth::check()) {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link verifikasi tidak valid.']);
        }

        // Validasi hash (SHA1 dari email — standar Laravel)
        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link verifikasi tidak valid atau sudah kadaluarsa.']);
        }

        // Validasi signature URL
        if (!request()->hasValidSignature()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link verifikasi sudah kadaluarsa. Silakan kirim ulang.']);
        }

        // Auto-login user untuk proses verifikasi
        Auth::login($user);
    }

    $user = Auth::user();

    // Jika sudah verified sebelumnya
    if ($user->hasVerifiedEmail()) {
        return redirect()->intended(route('dashboard'))
            ->with('info', 'Email Anda sudah terverifikasi sebelumnya.');
    }

    // Proses verifikasi
    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    return redirect()->intended(route('dashboard'))
        ->with('success', 'Email berhasil diverifikasi! Selamat datang.');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

// =====================
// 2FA Routes
// Tidak pakai 'verified' — user belum tentu verified saat setup 2FA pertama kali
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/2fa/setup',    [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable',  [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/verify',   [TwoFactorController::class, 'showVerify'])->name('2fa.form');
    Route::post('/2fa/verify',  [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// =====================
// Password Change — dikecualikan dari password.expiry agar tidak loop
// =====================
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::get('/password/change',  [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

// =====================
// AUTHENTICATED ROUTES (terproteksi penuh)
// =====================
Route::middleware(['auth', 'verified', '2fa', 'password.expiry'])->group(function () {

    // Profile & Security Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN PANEL
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });

    // SUPPORT PANEL
    Route::prefix('support')->name('support.')->middleware('role:support|admin')->group(function () {
        Route::get('/dashboard', fn() => view('support.dashboard'))->name('dashboard');
    });

    // PUBLIC USER AREA
    Route::prefix('laporan')->name('public.')->middleware('role:public')->group(function () {
        Route::get('/', fn() => view('public.dashboard'))->name('dashboard');
    });
});
