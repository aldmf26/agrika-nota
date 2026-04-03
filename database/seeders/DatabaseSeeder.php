<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles & permissions first
        $this->call(RolePermissionSeeder::class);

        // Seed master data (divisi)
        $this->call(DivisiSeeder::class);

        // Create test users dengan roles
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        $approver = User::factory()->create([
            'name' => 'Approver User',
            'email' => 'approver@example.com',
        ]);
        $approver->assignRole('approver');

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);
        $superAdmin->assignRole('super_admin');

        // Seed test data (notas)
        $this->call(TestDataSeeder::class);
    }
}
