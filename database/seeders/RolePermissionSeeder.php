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
            // Ticket permissions
            'ticket.create',
            'ticket.view.own',
            'ticket.view.all',
            'ticket.update.status',
            'ticket.assign',
            'ticket.delete',
            // User management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            // Reports
            'report.view',
            'report.export',
            // System
            'system.settings',
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles & assign permissions
        $publicRole = Role::firstOrCreate(['name' => 'public']);
        $publicRole->syncPermissions([
            'ticket.create',
            'ticket.view.own',
        ]);

        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $supportRole->syncPermissions([
            'ticket.view.all',
            'ticket.update.status',
            'ticket.assign',
            'report.view',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
    }
}
