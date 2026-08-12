<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditPendingNotaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $otherAdmin;

    private User $superAdmin;

    private User $approver;

    private Divisi $divisi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->otherAdmin = User::factory()->create();
        $this->otherAdmin->assignRole('admin');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
        $this->approver = User::factory()->create();
        $this->approver->assignRole('approver');
        $this->divisi = Divisi::create(['nama' => 'Aga', 'kode' => 'AGA', 'aktif' => true]);
    }

    public function test_pending_owner_and_super_admin_can_edit_but_others_cannot(): void
    {
        $nota = Nota::factory()->pending()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id]);

        $this->actingAs($this->admin)->get(route('nota.edit', $nota))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('nota.edit', $nota))->assertOk();
        $this->actingAs($this->otherAdmin)->get(route('nota.edit', $nota))->assertForbidden();
        $this->actingAs($this->approver)->get(route('nota.edit', $nota))->assertForbidden();
    }

    public function test_approved_and_void_notas_are_locked(): void
    {
        foreach (['approved', 'void'] as $status) {
            $nota = Nota::factory()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'status' => $status]);
            $this->actingAs($this->admin)->get(route('nota.edit', $nota))->assertForbidden();
            $this->actingAs($this->superAdmin)->get(route('nota.edit', $nota))->assertForbidden();
        }
    }

    public function test_pending_stays_pending_and_rejected_returns_to_pending_after_update(): void
    {
        foreach (['pending', 'rejected'] as $status) {
            $nota = Nota::factory()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'status' => $status]);

            $this->actingAs($this->admin)->put(route('nota.update', $nota), [
                'tipe' => 'biasa',
                'tanggal_nota' => now()->toDateString(),
                'divisi_id' => $this->divisi->id,
                'nomor_nota' => $nota->nomor_nota,
                'keterangan' => 'Keterangan sudah diperbarui',
                'nominal' => 250000,
            ])->assertRedirect(route('nota.show', $nota));

            $this->assertSame('pending', $nota->fresh()->status);
            $this->assertSame('Keterangan sudah diperbarui', $nota->fresh()->keterangan);
        }
    }

    public function test_toast_renders_below_header_with_close_button_and_timeout(): void
    {
        $nota = Nota::factory()->pending()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id]);

        $this->actingAs($this->admin)
            ->withSession(['success' => 'Nota berhasil diperbarui'])
            ->get(route('nota.show', $nota))
            ->assertOk()
            ->assertSee('top-20', false)
            ->assertSee('closeGlobalToast', false)
            ->assertSee('5000', false)
            ->assertDontSee('animate-pulse', false);
    }
}
