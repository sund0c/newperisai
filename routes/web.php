<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\KlasifikasiAsetController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\TahunAktifController;
use App\Http\Controllers\Admin\TahunContextController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrivacyController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// =====================
// PUBLIC ROUTES
// =====================
Route::get('/', fn() => redirect()->route('login'));

Route::get('/kebijakan-privasi', [PrivacyController::class, 'index'])
    ->name('privacy.index');

// Halaman maintenance — dapat diakses tanpa auth (untuk public yang di-redirect)
Route::get('/maintenance', fn() => view('maintenance'))->name('maintenance');

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
// maintenance.check diterapkan di sini — bypass untuk non-public
// =====================
Route::middleware(['auth', 'verified', '2fa', 'account.deletion', 'password.expiry', 'maintenance.check'])->group(function () {

    // Profile & Security Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/delete-request', [ProfileController::class, 'requestDeletion'])->name('profile.delete-request');
    Route::post('/profile/cancel-deletion', [ProfileController::class, 'cancelDeletion'])->name('profile.cancel-deletion');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN PANEL
    //    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
    Route::prefix('admin')->name('admin.')->middleware(['role:admin', 'tahun.context'])->group(function () {

        Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
        Route::patch('users/{user}/update', [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('users/{user}/restore', [UserController::class, 'restore'])
            ->name('users.restore')
            ->withTrashed();
        // Maintenance toggle
        Route::post('/maintenance/toggle', [MaintenanceController::class, 'toggle'])
            ->name('maintenance.toggle');

        Route::get('klasifikasi', [KlasifikasiAsetController::class, 'index'])->name('klasifikasi.index');
        Route::get('klasifikasi/{klasifikasi}', [KlasifikasiAsetController::class, 'show'])->name('klasifikasi.show');
        Route::post('klasifikasi/{klasifikasi}/subklas', [KlasifikasiAsetController::class, 'storeSubklas'])->name('klasifikasi.subklas.store');
        Route::patch('klasifikasi/{klasifikasi}/subklas/{subklasifikasi}', [KlasifikasiAsetController::class, 'updateSubklas'])->name('klasifikasi.subklas.update');
        Route::delete('klasifikasi/{klasifikasi}/subklas/{subklasifikasi}', [KlasifikasiAsetController::class, 'destroySubklas'])->name('klasifikasi.subklas.destroy');
        Route::patch('klasifikasi/{klasifikasi}/subklas/{subklasifikasi}/restore', [KlasifikasiAsetController::class, 'restoreSubklas'])->name('klasifikasi.subklas.restore');

        Route::get('opd', [OpdController::class, 'index'])->name('opd.index');
        Route::post('opd', [OpdController::class, 'store'])->name('opd.store');
        Route::patch('opd/{opd}', [OpdController::class, 'update'])->name('opd.update');
        Route::delete('opd/{opd}', [OpdController::class, 'destroy'])->name('opd.destroy');
        Route::patch('opd/{id}/restore', [OpdController::class, 'restore'])->name('opd.restore');

        Route::get('periods', [PeriodController::class, 'index'])->name('periods.index');
        Route::post('periods', [PeriodController::class, 'store'])->name('periods.store');
        Route::patch('periods/{period}', [PeriodController::class, 'update'])->name('periods.update');
        Route::patch('periods/{period}/activate', [PeriodController::class, 'activate'])->name('periods.activate');
        Route::patch('periods/{period}/deactivate', [PeriodController::class, 'deactivate'])->name('periods.deactivate');
        Route::delete('periods/{period}', [PeriodController::class, 'destroy'])->name('periods.destroy');

        Route::get('tahunaktif',                          [TahunAktifController::class, 'index'])->name('tahunaktif.index');
        Route::post('tahunaktif',                         [TahunAktifController::class, 'store'])->name('tahunaktif.store');
        Route::post('tahunaktif/set-context',             [TahunContextController::class, 'setContext'])->name('tahunaktif.set-context'); // ← HARUS sebelum /{tahunAktif}
        Route::patch('tahunaktif/{tahunAktif}/activate',  [TahunAktifController::class, 'activate'])->name('tahunaktif.activate');
        Route::patch('tahunaktif/{tahunAktif}/deactivate', [TahunAktifController::class, 'deactivate'])->name('tahunaktif.deactivate');
        Route::delete('tahunaktif/{tahunAktif}',          [TahunAktifController::class, 'destroy'])->name('tahunaktif.destroy');


        Route::get('assets',                     [AssetController::class, 'index'])->name('assets.index');
        Route::post('assets',                    [AssetController::class, 'store'])->name('assets.store');
        Route::get('assets/generate-kode', [AssetController::class, 'generateKode'])->name('assets.generate-kode');
        Route::put('assets/{asset}',              [AssetController::class, 'update'])->name('assets.update');
        Route::delete('assets/{asset}',           [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::patch('assets/{id}/restore',       [AssetController::class, 'restore'])->name('assets.restore');
    });
});
