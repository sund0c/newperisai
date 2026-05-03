<?php
// database/seeders/UserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // ADMIN USERS (3)
        // Admin utama: must_change_password = true (wajib ganti saat login pertama)
        // Password diambil dari .env agar tidak hardcode di git
        // =====================
        $admins = [
            [
                'name'                 => 'Admin PERISAI',
                'email'                => 'putu.sundika@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_ADMIN_PASSWORD')),
                'opd_id'         => 1,
                'is_active'            => true,
                'email_verified_at' => null,
                'password_changed_at'  => now(),
                'must_change_password' => false,
            ],

        ];

        foreach ($admins as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->markEmailAsVerified();
            $user->assignRole('admin');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }

        // =====================
        // VERIFICATOR USERS
        // =====================
        $supports = [
            [
                'name'                 => 'Verifikator-01',
                'email'                => 'verifikator1@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_VERIFIKATOR_PASSWORD')),
                'opd_id'         => 1,
                'is_active'            => true,
                'email_verified_at' => null,
                'password_changed_at'  => now(),
                'must_change_password' => false,
            ],

        ];

        foreach ($supports as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->markEmailAsVerified();
            $user->assignRole('verifikator');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }

        // =====================
        // AUDITOR USERS
        // =====================
        $supports = [
            [
                'name'                 => 'Auditor-01',
                'email'                => 'auditor1@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_AUDITOR_PASSWORD')),
                'opd_id'         => 1,
                'is_active'            => true,
                'email_verified_at' => null,
                'password_changed_at'  => now(),
                'must_change_password' => false,
            ],

        ];

        foreach ($supports as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->markEmailAsVerified();
            $user->assignRole('auditor');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }


        // =====================
        // PUBLIC OPD
        // =====================
        if (app()->environment('local', 'development', 'staging')) {
            $publics = [
                [
                    'name'                 => 'OPD A',
                    'email'                => 'diskominfos@baliprov.go.id',
                    'password'             => Hash::make(env('SEEDER_OPD_PASSWORD')),
                    'opd_id'         => 2,
                    'is_active'            => true,
                    'email_verified_at' => null,
                    'password_changed_at'  => now(),
                    'must_change_password' => false,
                ],

            ];

            foreach ($publics as $data) {
                $user = User::firstOrCreate(['email' => $data['email']], $data);
                $user->markEmailAsVerified();
                $user->assignRole('opd');
                PasswordHistory::firstOrCreate(
                    ['user_id' => $user->id, 'password' => $data['password']]
                );
            }
        }
    }
}
