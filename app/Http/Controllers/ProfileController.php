<?php

namespace App\Http\Controllers;


use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\SandidataMiddleware;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', [
            'user'      => $user,
            'daysLeft'  => $user->daysUntilPasswordExpiry(),
            'expiresAt' => $user->passwordExpiresAt()->format('d M Y'),
        ]);
    }

    /**
     * Update nama, email, phone, organization
     */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'organization' => ['required', 'string', 'max:255'],
        ]);

        $old = $user->only(['name', 'phone', 'organization']);

        $phone = $request->phone
            ? SandidataMiddleware::encryptValue(strip_tags($request->phone))
            : null;

        $user->update([
            'name'         => strip_tags($request->name),
            'phone'        => $phone,
            'organization' => strip_tags($request->organization),
        ]);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'profile_updated',
            'old_values' => $old,
            'new_values' => $user->only(['name', 'phone', 'organization']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password dari halaman profil
     */
    public function updatePassword(Request $request)
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

        if ($user->isPasswordReused($newPassword)) {
            return back()->withErrors([
                'password' => 'Password ini pernah digunakan sebelumnya. Gunakan password berbeda dari '
                    . $user::PASSWORD_HISTORY_LIMIT . ' password terakhir.',
            ])->withInput()->with('active_tab', 'password');
        }

        $user->rotatePassword(Hash::make($newPassword));

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'password_changed',
            'new_values' => ['reason' => 'user_self_service'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);


        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Password berhasil diperbarui. Silakan login kembali.');

        //return back()->with('success', 'Password berhasil diperbarui.')->with('active_tab', 'password');
    }
}
