<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Middleware\SandidataMiddleware;


class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'        => ['nullable', 'string'],
            'organization' => ['required', 'string', 'max:255'],
            'password'     => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(), // cek di haveibeenpwned.com
            ],
            'terms' => ['required', 'accepted'],
        ])->validate();

        return DB::transaction(function () use ($input) {

            $phone = null;
            if (!empty($input['phone'])) {
                [$encrypted, $error] = SandidataMiddleware::seal($input['phone']);
                if (!$error && $encrypted) {
                    $json = json_decode($encrypted, true);
                    $phone = $json['Ciphertext'][0]['text'] ?? $input['phone'];
                } else {
                    $phone = $input['phone']; // fallback simpan plaintext kalau SEAL gagal
                }
            }


            $hashedPassword = Hash::make($input['password']);

            $user = User::create([
                'name'                => strip_tags($input['name']),
                'email'               => $input['email'],
                'password'            => $hashedPassword,
                'phone'               => $input['phone'] ?? null,
                'organization'        => strip_tags($input['organization']),
                'password_changed_at' => now(), // mulai hitung rotasi dari sekarang
                'must_change_password' => false,
            ]);

            $user->assignRole('public');

            // Simpan ke password history (entry pertama)
            PasswordHistory::create([
                'user_id'  => $user->id,
                'password' => $hashedPassword,
            ]);

            \App\Models\AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'register',
                'new_values' => ['email' => $user->email, 'organization' => $user->organization],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $user;
        });
    }
}
