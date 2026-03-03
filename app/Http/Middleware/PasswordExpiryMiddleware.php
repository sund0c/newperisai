<?php
// app/Http/Middleware/PasswordExpiryMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordExpiryMiddleware
{
    /**
     * Route yang boleh diakses walau password expired / must_change_password.
     * Jangan sampai redirect loop!
     */
    protected array $except = [
        'password.change',          // halaman ganti password
        'password.change.update',   // proses ganti password
        'logout',
        'profile.index',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Skip untuk route yang dikecualikan
        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        // Kondisi 1: Admin paksa ganti password (must_change_password = true)
        if ($user->must_change_password) {
            return redirect()->route('password.change')
                ->with('warning', 'Password Anda telah direset oleh administrator. Silakan buat password baru.');
        }

        // Kondisi 2: Password sudah kadaluarsa (> 2 bulan)
        if ($user->isPasswordExpired()) {
            return redirect()->route('password.change')
                ->with('warning', 'Password Anda sudah lebih dari 2 bulan. Wajib diganti untuk keamanan akun.');
        }

        return $next($request);
    }
}
