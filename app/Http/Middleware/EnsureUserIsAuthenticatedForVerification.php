<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Middleware ini memastikan user ter-autentikasi saat mengklik link verifikasi email.
 * Jika user belum login tapi link verifikasi valid, login-kan user secara otomatis.
 *
 * Ini mengatasi masalah: user klik link di email client yang berbeda browser
 * sehingga session tidak ada, padahal link verifikasi valid.
 */
class EnsureUserIsAuthenticatedForVerification
{
    public function handle(Request $request, Closure $next)
    {
        // Jika sudah login, lanjutkan normal
        if (Auth::check()) {
            return $next($request);
        }

        // Ambil user dari URL parameter
        $id = $request->route('id');
        $hash = $request->route('hash');

        if (!$id || !$hash) {
            return redirect()->route('login');
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link verifikasi tidak valid.']);
        }

        // Validasi hash
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link verifikasi tidak valid atau sudah kadaluarsa.']);
        }

        // Login user sementara untuk proses verifikasi
        Auth::login($user);

        return $next($request);
    }
}
