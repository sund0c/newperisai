<?php

namespace App\Actions\Fortify;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    public function update(mixed $user, array $input): void
    {
        Validator::make($input, [
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
        ])->validate();

        $newPassword = $input['password'];

        // ── Cek: password baru tidak boleh sama dengan password lama ──
        if ($user->isPasswordReused($newPassword)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => [
                    'Password ini pernah digunakan sebelumnya. '
                        . 'Gunakan password yang berbeda dari ' . $user::PASSWORD_HISTORY_LIMIT . ' password terakhir.'
                ],
            ]);
        }

        // ── Rotate password (simpan lama ke history, update baru) ─────
        $user->rotatePassword(Hash::make($newPassword));

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'password_changed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
