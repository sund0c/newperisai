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
                'email'                => 'putu.sundika@baliprov.go.id',
                'password'             => Hash::make(env('SEEDER_ADMIN1_PASSWORD')),
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
                'name'                 => 'Support-01',
                'email'                => 'bidpersandianbali@gmail.com',
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
                    'name'                 => 'Putu Sundika',
                    'email'                => 'putu.sundika@gmail.com',
                    'password'             => Hash::make(env('SEEDER_PUBLIC1_PASSWORD')),
                    'organization'         => 'personal',
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
                'name'                 => 'CSIRT-01',
                'email'                => 'anantaw81@gmail.com',
                'password'             => Hash::make(env('SEEDER_CSIRT1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
            [
                'name'                 => 'CSIRT-02',
                'email'                => 'arircbm@gmail.com',
                'password'             => Hash::make(env('SEEDER_CSIRT1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
            [
                'name'                 => 'CSIRT-03',
                'email'                => 'esamahadi.office@gmail.com',
                'password'             => Hash::make(env('SEEDER_CSIRT1_PASSWORD')),
                'organization'         => 'CSIRT Provinsi Bali',
                'is_active'            => true,
                'email_verified_at'    => now(),
                'password_changed_at'  => null,
                'must_change_password' => true,
            ],
            [
                'name'                 => 'CSIRT-04',
                'email'                => 'OmanJaya53@gmail.com',
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
                'name'                 => 'DPO-01',
                'email'                => 'widiartha.made@gmail.com',
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
