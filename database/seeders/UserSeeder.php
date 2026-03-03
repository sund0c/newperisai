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
        // =====================
        $admins = [
            [
                'name'                => 'Admin CSIRT Bali',
                'email'               => 'admin@csirt.baliprov.go.id',
                'password'            => Hash::make('Admin@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Admin Dua',
                'email'               => 'admin2@csirt.baliprov.go.id',
                'password'            => Hash::make('Admin2@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Admin Tiga',
                'email'               => 'admin3@csirt.baliprov.go.id',
                'password'            => Hash::make('Admin3@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
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
                'name'                => 'Support Satu',
                'email'               => 'support1@csirt.baliprov.go.id',
                'password'            => Hash::make('Support1@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Support Dua',
                'email'               => 'support2@csirt.baliprov.go.id',
                'password'            => Hash::make('Support2@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Support Tiga',
                'email'               => 'support3@csirt.baliprov.go.id',
                'password'            => Hash::make('Support3@CSIRT2024!'),
                'organization'        => 'CSIRT Provinsi Bali',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
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
        // PUBLIC USERS (3)
        // =====================
        $publics = [
            [
                'name'                => 'Budi Santoso',
                'email'               => 'budi.santoso@gmail.com',
                'password'            => Hash::make('Public1@Test2024!'),
                'organization'        => 'Dinas Kominfo Bali',
                'phone'               => '08123456789',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Made Wijaya',
                'email'               => 'made.wijaya@gmail.com',
                'password'            => Hash::make('Public2@Test2024!'),
                'organization'        => 'BSSN',
                'phone'               => '08234567890',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ],
            [
                'name'                => 'Ni Luh Ayu',
                'email'               => 'niluh.ayu@gmail.com',
                'password'            => Hash::make('Public3@Test2024!'),
                'organization'        => 'Masyarakat Umum',
                'phone'               => '08345678901',
                'is_active'           => true,
                'email_verified_at'   => now(),
                'password_changed_at' => now(),
                'must_change_password' => false,
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
}
