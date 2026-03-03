<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;

// =====================
// PUBLIC ROUTES
// =====================
Route::get('/', fn() => redirect()->route('login'));

// =====================
// AUTH ROUTES (Fortify handles: login, register, logout, forgot-password, reset-password)
// =====================

// 2FA Routes (hanya butuh auth, belum perlu password.expiry)
Route::middleware('auth')->group(function () {
    Route::get('/2fa/setup',    [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable',  [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/verify',   [TwoFactorController::class, 'showVerify'])->name('2fa.form');
    Route::post('/2fa/verify',  [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// Password Change (expired / force) — dikecualikan dari password.expiry agar tidak loop
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::get('/password/change',  [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

// =====================
// AUTHENTICATED ROUTES (terproteksi penuh)
// =====================
Route::middleware(['auth', 'verified', '2fa', 'password.expiry'])->group(function () {

    // Dashboard (redirect by role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =====================
    // ADMIN PANEL
    // =====================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });

    // =====================
    // SUPPORT PANEL
    // =====================
    Route::prefix('support')->name('support.')->middleware('role:support|admin')->group(function () {
        Route::get('/dashboard', fn() => view('support.dashboard'))->name('dashboard');
    });

    // =====================
    // PUBLIC USER AREA
    // =====================
    Route::prefix('laporan')->name('public.')->middleware('role:public')->group(function () {
        Route::get('/', fn() => view('public.dashboard'))->name('dashboard');
    });

    // Profile & Security Settings
    Route::get('/profile', fn() => view('profile.index'))->name('profile.index');
});
