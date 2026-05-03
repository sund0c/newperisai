<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [

            // User management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // System
            'system.settings',
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }


        $opdRole = Role::firstOrCreate(['name' => 'opd']);
        /*$opdRole->syncPermissions([
            'ticket.create',
            'ticket.view.own',
        ]);*/

        $verifikatorRole = Role::firstOrCreate(['name' => 'verifikator']);
        /*$opdRole->syncPermissions([
            'ticket.create',
            'ticket.view.own',
        ]);*/

        $auditorRole = Role::firstOrCreate(['name' => 'auditor']);
        /*$opdRole->syncPermissions([
            'ticket.create',
            'ticket.view.own',
        ]);*/

        // ADMIN — akses penuh
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
    }
}
