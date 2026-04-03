<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's role & permission.
     * 
     * Run: php artisan db:seed --class=RolePermissionSeeder
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // ===== CREATE PERMISSIONS =====
        $permissions = [
            // Nota permissions
            'nota.view-own',
            'nota.view-all',
            'nota.create',
            'nota.edit-own',
            'nota.edit-all',
            'nota.approve',
            'nota.reject',
            'nota.void',
            'nota.export',

            // User management
            'user.manage',

            // Divisi management
            'divisi.manage',

            // Deposit log
            'deposit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ===== CREATE ROLES =====

        // 1. ADMIN - Input nota, lihat nota milik sendiri, edit draft
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'nota.view-own',
            'nota.create',
            'nota.edit-own',
            'deposit.view',
        ]);

        // 2. APPROVER - Review semua nota, approve/reject, tambah catatan
        $approverRole = Role::firstOrCreate(['name' => 'approver']);
        $approverRole->syncPermissions([
            'nota.view-all',
            'nota.approve',
            'nota.reject',
            'nota.void',
            'nota.export',
            'deposit.view',
        ]);

        // 3. SUPER_ADMIN - Semua akses + kelola user + kelola divisi/lokasi
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions($permissions);
    }
}
