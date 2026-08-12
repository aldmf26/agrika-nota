<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_super_admin_can_create_and_update_user(): void
    {
        $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Miss Tesa',
            'email' => 'tesa@example.com',
            'password' => 'password123',
            'role' => 'approver',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'tesa@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('approver'));

        $this->actingAs($this->superAdmin)->put(route('admin.users.update', $user), [
            'name' => 'Tesa Updated',
            'email' => 'tesa.updated@example.com',
            'password' => '',
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Tesa Updated', $user->name);
        $this->assertSame('tesa.updated@example.com', $user->email);
        $this->assertTrue($user->hasRole('admin'));
    }
}
