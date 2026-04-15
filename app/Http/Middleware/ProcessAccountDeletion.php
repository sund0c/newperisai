<?php

namespace App\Http\Middleware;

use App\Models\AccountDeletionRequest;
use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: ProcessAccountDeletion
 *
 * Pola identik dengan password.expiry — dicek saat request masuk,
 * bukan di background scheduler/cron.
 *
 * Alur:
 *  1. User login kembali dalam 48 jam → bisa batalkan di halaman profil
 *  2. User login setelah 48 jam       → akun langsung dieksekusi hapus di sini
 *  3. User tidak pernah login lagi    → akun dihapus saat pertama kali ada
 *                                       request masuk setelah 48 jam
 *                                       (misal: admin membuka profil user tsb)
 */
class ProcessAccountDeletion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Cari permintaan penghapusan yang sudah jatuh tempo
        $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->whereNull('cancelled_at')
            ->where('scheduled_at', '<=', now())
            ->first();

        if (! $deletionRequest) {
            return $next($request);
        }

        // ── Eksekusi penghapusan ─────────────────────────────────────────
        DB::transaction(function () use ($user, $deletionRequest, $request) {

            // 1. Anonimkan laporan — user_id sudah NULL via nullOnDelete(),
            //    tapi kita overwrite kolom snapshot secara eksplisit.
            DB::table('reports')
                ->where('user_id', $user->id)
                ->update([
                    'user_id'        => null,
                    'reporter_name'  => 'Anonim',
                    'reporter_email' => null,
                    'reporter_org'   => 'Tidak Diketahui',
                    'updated_at'     => now(),
                ]);

            // 2. Audit log — catat sebelum user hilang
            AuditLog::create([
                'user_id'    => null,
                'action'     => 'account_deleted',
                'old_values' => [
                    'email'        => $user->email,
                    'name'         => $user->name,
                    'organization' => $user->organization,
                    'reason'       => $deletionRequest->reason,
                    'requested_at' => $deletionRequest->created_at->toIso8601String(),
                    'scheduled_at' => $deletionRequest->scheduled_at->toIso8601String(),
                ],
                'new_values'  => ['status' => 'deleted_by_request'],
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            // 3. Hapus deletion request
            $deletionRequest->delete();

            // 4. Revoke semua token autentikasi
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // 5. Hapus user — FK nullOnDelete sudah handle sisanya
            $user->delete();

            Log::info('Account deletion executed on login', [
                'email' => $user->email,
            ]);
        });

        // Logout dan redirect ke login dengan pesan
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Akun Anda telah dihapus sesuai permintaan sebelumnya.');
    }
}
