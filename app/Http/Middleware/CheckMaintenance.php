<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    /**
     * Role yang tetap bisa mengakses sistem saat maintenance.
     * Public user akan diarahkan ke halaman maintenance.
     */
    private const BYPASS_ROLES = ['admin', 'auditor', 'verifiator'];

    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah maintenance aktif
        if (!Setting::maintenanceActive()) {
            return $next($request);
        }

        // Jika belum login → arahkan ke halaman maintenance (bukan login)
        // agar publik tidak bisa melakukan apa-apa
        if (!$request->user()) {
            return $this->maintenanceResponse($request);
        }

        // Jika punya salah satu bypass role → lanjut normal
        foreach (self::BYPASS_ROLES as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Role public → tampilkan halaman maintenance
        return $this->maintenanceResponse($request);
    }

    private function maintenanceResponse(Request $request): Response
    {
        // Jika request JSON/AJAX → kembalikan JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sistem sedang dalam pemeliharaan. Silakan hubungi csirt@baliprov.go.id',
            ], 503);
        }

        return response()
            ->view('maintenance', [], 503)
            ->header('Retry-After', 3600);
    }
}
