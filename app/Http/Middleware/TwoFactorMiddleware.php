<?php
// app/Http/Middleware/TwoFactorMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Hanya berlaku jika user mengaktifkan 2FA secara sukarela
        if ($user && $user->hasTwoFactorEnabled()) {
            if (!$request->session()->get('2fa_verified')) {
                return redirect()->route('2fa.form');
            }
        }

        return $next($request);
    }
}
