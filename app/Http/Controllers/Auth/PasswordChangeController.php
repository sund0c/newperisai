<?php
// app/Http/Controllers/Auth/PasswordChangeController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Halaman ganti password (expired / force by admin).
     */
    public function show()
    {
        $user = auth()->user();

        return view('auth.password-change', [
            'daysLeft'   => $user->daysUntilPasswordExpiry(),
            'expiresAt'  => $user->passwordExpiresAt()->format('d M Y'),
            'isForced'   => $user->must_change_password || $user->isPasswordExpired(),
        ]);
    }

    /**
     * Proses ganti password dari halaman wajib.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
        ]);

        $user        = auth()->user();
        $newPassword = $request->password;

        // Cek reuse password
        if ($user->isPasswordReused($newPassword)) {
            return back()->withErrors([
                'password' => 'Password ini pernah digunakan sebelumnya. '
                    . 'Gunakan password yang berbeda dari ' . $user::PASSWORD_HISTORY_LIMIT . ' password terakhir.',
            ]);
        }

        // Rotate password
        $user->rotatePassword(Hash::make($newPassword));

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'password_rotated',
            'new_values' => ['reason' => $user->must_change_password ? 'admin_forced' : 'expired'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password berhasil diperbarui. Pastikan Anda mengingatnya!');
    }
}
