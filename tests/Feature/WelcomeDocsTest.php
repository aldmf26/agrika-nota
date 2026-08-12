<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeDocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_portal_shows_login_and_guide_actions(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Agrika Nota')
            ->assertSee(route('login'), false)
            ->assertSee(route('docs.index'), false)
            ->assertDontSee('Buka Dashboard');
    }

    public function test_authenticated_portal_shows_dashboard_action(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('Buka Dashboard')
            ->assertSee(route('dashboard'), false);
    }

    public function test_public_guides_describe_current_workflow(): void
    {
        $this->get(route('docs.user-guide'))
            ->assertOk()
            ->assertSee('maksimal 20 file')
            ->assertSee('Lampiran bersifat opsional');

        $this->get(route('docs.workflow'))
            ->assertOk()
            ->assertSee('Super Admin / IT')
            ->assertSee('Pemeriksa')
            ->assertSee('Ada Tambahan')
            ->assertDontSee('Approver mengecek detail');
    }
}
