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
            // CSIRT mitigasi
            'csirt.view',
            'csirt.process',
            // DPO  mitigasi
            'dpo.view',
            'dpo.process',
            // System
            'system.settings',
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // PUBLIC — hanya bisa buat dan lihat tiket sendiri
        $publicRole = Role::firstOrCreate(['name' => 'public']);
        $publicRole->syncPermissions([
            'ticket.create',
            'ticket.view.own',
        ]);

        // SUPPORT — validasi tiket, lihat semua tiket
        $supportRole = Role::firstOrCreate(['name' => 'support']);
        $supportRole->syncPermissions([
            'ticket.view.all',
            'ticket.update.status',
            'ticket.assign',
            'report.view',
            'csirt.view', // bisa lihat progress CSIRT di detail tiket
        ]);

        // CSIRT — hanya akses proses mitigasi, tidak terhubung ke ticketing public
        $csirtRole = Role::firstOrCreate(['name' => 'csirt']);
        $csirtRole->syncPermissions([
            'csirt.view',
            'csirt.process',
            'ticket.view.all', // baca detail tiket untuk keperluan mitigasi
        ]);

        // DPO — hanya akses proses mitigasi, tidak terhubung ke ticketing public
        $dpoRole = Role::firstOrCreate(['name' => 'dpo']);
        $dpoRole->syncPermissions([
            'dpo.view',
            'dpo.process',
            'ticket.view.all', // baca detail tiket untuk keperluan mitigasi
        ]);

        // ADMIN — akses penuh
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
    }
}
