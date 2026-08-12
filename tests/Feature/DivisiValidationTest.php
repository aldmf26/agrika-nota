<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_divisions_in_same_batch_return_validation_error_without_partial_insert(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)
            ->from(route('admin.divisi.index'))
            ->post(route('admin.divisi.store'), [
                'items' => [
                    ['nama' => 'Tes', 'kode' => 'TES'],
                    ['nama' => 'tes', 'kode' => 'tes'],
                ],
            ]);

        $response->assertRedirect(route('admin.divisi.index'))
            ->assertSessionHasErrors(['items.1.nama', 'items.1.kode']);
        $this->assertSame(0, Divisi::count());
    }

    public function test_validation_errors_are_rendered_by_global_toast(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->from(route('admin.divisi.index'))
            ->followingRedirects()
            ->post(route('admin.divisi.store'), ['items' => []])
            ->assertOk()
            ->assertSee('id="globalToast"', false)
            ->assertSee('items field is required');
    }
}
