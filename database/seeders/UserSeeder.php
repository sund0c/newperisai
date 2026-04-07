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
                'name'                 => 'Admin CSIRT Bali',
                'email'                => 'admin1@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_ADMIN1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
            [
                'name'                 => 'Admin Dua',
                'email'                => 'admin2@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_ADMIN2_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
            [
                'name'                 => 'Admin Tiga',
                'email'                => 'admin3@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_ADMIN3_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
        ];

        foreach ($admins as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->assignRole('admin');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }

        // =====================
        // SUPPORT USERS (3)
        // =====================
        $supports = [
            [
                'name'                 => 'Support Satu',
                'email'                => 'yopila7792@fpxnet.com',
                'password'             => Hash::make(env('SEEDER_SUPPORT1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],

        ];

        foreach ($supports as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->assignRole('support');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }

        // =====================
        // PUBLIC USERS (3) — hanya untuk development/testing
        // Hapus blok ini sebelum deploy ke production
        // =====================
        if (app()->environment('local', 'development', 'staging')) {
            $publics = [
                [
                    'name'                 => 'Piyor Public',
                    'email'                => 'simifar488@bpotogo.com',
                    'password'             => Hash::make(env('SEEDER_PUBLIC1_PASSWORD')),
                    'organization'         => 'Dinas Kominfo Bali',
                    'phone'                => '08123456789',
                    'is_active'            => true,
                    'email_verified_at'    => now(),
                    'password_changed_at'  => null,
                    'must_change_password' => true,
                ],

            ];

            foreach ($publics as $data) {
                $user = User::firstOrCreate(['email' => $data['email']], $data);
                $user->assignRole('public');
                PasswordHistory::firstOrCreate(
                    ['user_id' => $user->id, 'password' => $data['password']]
                );
            }
        }

        // =====================
        // CSIRT USERS (3)
        // =====================
        $csirts = [
            [
                'name'                 => 'CSIRT Satu',
                'email'                => 'focapi8325@kobace.com',
                'password'             => Hash::make(env('SEEDER_CSIRT1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],

        ];

        foreach ($csirts as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->assignRole('csirt');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }

        // =====================
        // DPO USERS (3)
        // =====================
        $dpo = [
            [
                'name'                 => 'DPO satu',
                'email'                => 'dpo1@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_DPO1_PASSWORD')),
                'organization'         => 'DPO Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],

        ];

        foreach ($dpo as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->assignRole('dpo');
            PasswordHistory::firstOrCreate(
                ['user_id' => $user->id, 'password' => $data['password']]
            );
        }
    }
}
