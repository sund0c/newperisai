<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\OpdSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            OpdSeeder::class,
            UserSeeder::class,
            KlasifikasiSeeder::class,
        ]);
    }
}
