<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_nota_uses_short_random_token_instead_of_nota_number(): void
    {
        $nota = Nota::factory()->create(['nomor_nota' => 'MUDAH-DITEBAK']);

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{12}$/', $nota->public_token);
        $this->get(route('nota.public_view', ['token' => $nota->public_token]))
            ->assertOk()
            ->assertSee('MUDAH-DITEBAK');
        $this->get('/v/MUDAH-DITEBAK')->assertNotFound();
    }

    public function test_authenticated_nota_links_use_public_token_instead_of_database_id(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $nota = Nota::factory()->create(['user_id' => $admin->id]);

        $this->assertSame("/nota/{$nota->public_token}", route('nota.show', $nota, false));

        $this->actingAs($admin)
            ->get(route('nota.index'))
            ->assertOk()
            ->assertSee(route('nota.show', $nota, false), false)
            ->assertDontSee("/nota/{$nota->id}\"", false);
    }

    public function test_login_is_rate_limited_after_five_attempts_for_same_identity_and_ip(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => 'user@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_admin_division_filter_cannot_escape_own_nota_scope(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $otherUser = User::factory()->create();
        $firstDivisi = Divisi::create(['nama' => 'Divisi Pertama', 'kode' => 'DP', 'is_active' => true]);
        $filteredDivisi = Divisi::create(['nama' => 'Divisi Filter', 'kode' => 'DF', 'is_active' => true]);

        Nota::factory()->create([
            'user_id' => $admin->id,
            'divisi_id' => $firstDivisi->id,
            'nomor_nota' => 'MILIK-SENDIRI',
        ]);
        $otherNota = Nota::factory()->create([
            'user_id' => $otherUser->id,
            'divisi_id' => $firstDivisi->id,
            'tipe' => 'split',
            'nomor_nota' => 'RAHASIA-USER-LAIN',
        ]);
        NotaItem::create([
            'nota_id' => $otherNota->id,
            'divisi_id' => $filteredDivisi->id,
            'nominal' => 100000,
        ]);

        $this->actingAs($admin)
            ->get(route('nota.index', ['divisi_id' => $filteredDivisi->id]))
            ->assertOk()
            ->assertDontSee('RAHASIA-USER-LAIN');
    }
}
