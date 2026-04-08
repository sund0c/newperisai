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

        if (!$user) {
            return $next($request);
        }

        // Hanya berlaku jika 2FA aktif DAN secret tersedia
        // Edge case: flag enabled tapi secret null → jangan paksa verify (infinite loop)
        if ($user->google2fa_enabled && $user->google2fa_secret) {
            if (!$request->session()->get('2fa_verified')) {

                // Hindari redirect loop: jangan redirect jika sudah di route 2fa
                $currentRoute = $request->route()?->getName();
                $excluded = ['2fa.form', '2fa.verify', 'logout'];

                if (!in_array($currentRoute, $excluded)) {
                    return redirect()->route('2fa.form');
                }
            }
        }

        return $next($request);
    }
}
