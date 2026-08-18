<?php

namespace Tests\Feature;

use App\Models\Nota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotaQrVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_uses_banjarmasin_time_zone(): void
    {
        $this->assertSame('Asia/Makassar', config('app.timezone'));
        $this->assertSame('+08:00', now()->format('P'));
    }

    public function test_creator_verification_only_shows_nota_number_and_creator(): void
    {
        $creator = User::factory()->create(['name' => 'Pembuat Nota']);
        $nota = Nota::factory()->pending()->create([
            'user_id' => $creator->id,
            'nomor_nota' => 'NOTA-QR-001',
            'keterangan' => 'Rahasia transaksi tidak boleh tampil',
            'nominal' => 987654,
        ]);

        $this->get(route('nota.public_view', [$nota->public_token, 'creator']))
            ->assertOk()
            ->assertSee('NOTA-QR-001')
            ->assertSee('Pembuat Nota')
            ->assertDontSee('Rahasia transaksi tidak boleh tampil')
            ->assertDontSee('987654');
    }

    public function test_approval_verification_reflects_current_approval_status(): void
    {
        $pending = Nota::factory()->pending()->create();
        $approved = Nota::factory()->approved()->create();

        $this->get(route('nota.public_view', [$pending->public_token, 'approval']))
            ->assertOk()
            ->assertSee('Nota Belum Terverifikasi');

        $this->get(route('nota.public_view', [$approved->public_token, 'approval']))
            ->assertOk()
            ->assertSee('Nota Sudah Terverifikasi');
    }

    public function test_unknown_verification_type_returns_not_found(): void
    {
        $nota = Nota::factory()->create();

        $this->get("/v/{$nota->public_token}/unknown")->assertNotFound();
    }

    public function test_pending_print_keeps_manual_signature_and_only_shows_creator_qr(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $creator = User::factory()->create();
        $creator->assignRole('super_admin');
        $nota = Nota::factory()->pending()->create(['user_id' => $creator->id]);

        $this->actingAs($creator)
            ->get(route('nota.print', $nota))
            ->assertOk()
            ->assertSee('QR verifikasi pengaju')
            ->assertDontSee('QR verifikasi persetujuan')
            ->assertSee('Diketahui Oleh');
    }

    public function test_approved_print_shows_creator_and_approval_qr(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $creator = User::factory()->create();
        $creator->assignRole('super_admin');
        $nota = Nota::factory()->approved()->create(['user_id' => $creator->id]);

        $this->actingAs($creator)
            ->get(route('nota.print', $nota))
            ->assertOk()
            ->assertSee('QR verifikasi pengaju')
            ->assertSee('QR verifikasi persetujuan')
            ->assertSee('Diketahui Oleh');
    }

    public function test_detail_and_print_preserve_description_line_breaks_and_half_a4_layout(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $creator = User::factory()->create();
        $creator->assignRole('super_admin');
        $nota = Nota::factory()->pending()->create([
            'user_id' => $creator->id,
            'keterangan' => "Baris pertama\n\nBaris setelah jarak",
        ]);

        $this->actingAs($creator)
            ->get(route('nota.show', $nota))
            ->assertOk()
            ->assertSee('whitespace-pre-wrap', false)
            ->assertSee("Baris pertama\n\nBaris setelah jarak");

        $this->get(route('nota.print', $nota))
            ->assertOk()
            ->assertSee('size: A4 portrait', false)
            ->assertSee('width: 210mm', false)
            ->assertSee('height: 297mm', false)
            ->assertSee('grid-template-rows: 1fr 1fr', false)
            ->assertSee('margin-top: auto', false)
            ->assertSee('border-top: 1px dashed #555', false)
            ->assertSee('GUNTING')
            ->assertDontSee('ARSIP (PUTIH)')
            ->assertDontSee('COPY MISS TESA (PINK)')
            ->assertSee('watermark-copy', false)
            ->assertSee('transform: translate(-50%, -50%)', false)
            ->assertSee('rgba(220, 38, 38, 0.22)', false)
            ->assertSee('border: 3px solid rgba(220, 38, 38, 0.22)', false)
            ->assertSee('COPY')
            ->assertSeeInOrder(['Baris pertama', 'Baris pertama'])
            ->assertSee('class="nota-description"', false)
            ->assertSee("Baris pertama\n\nBaris setelah jarak");
    }

    public function test_split_and_revenue_print_use_compact_aligned_layout(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $creator = User::factory()->create();
        $creator->assignRole('super_admin');

        $split = Nota::factory()->pending()->split()->create(['user_id' => $creator->id]);
        $revenue = Nota::factory()->pending()->revenueSharing()->create(['user_id' => $creator->id]);

        $this->actingAs($creator)
            ->get(route('nota.print', $split))
            ->assertOk()
            ->assertSee('class="split-description"', false)
            ->assertSee('grid-template-columns: 82px 8px 120px', false)
            ->assertSee('justify-content: end', false)
            ->assertSee('watermark-copy-split', false)
            ->assertSee('Diajukan')
            ->assertSee('Oleh')
            ->assertSee('<span>:</span>', false);

        $this->get(route('nota.print', $revenue))
            ->assertOk()
            ->assertSee('class="calculation-note"', false)
            ->assertDontSee('margin-top: -20px', false);
    }

    public function test_only_super_admin_can_see_and_open_print(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $approver = User::factory()->create();
        $approver->assignRole('approver');
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');
        $nota = Nota::factory()->approved()->create(['user_id' => $admin->id]);
        $printUrl = route('nota.print', $nota, false);

        $this->actingAs($admin)
            ->get(route('nota.show', $nota))
            ->assertOk()
            ->assertDontSee($printUrl, false);
        $this->get($printUrl)->assertForbidden();

        $this->actingAs($approver)
            ->get(route('nota.show', $nota))
            ->assertOk()
            ->assertDontSee($printUrl, false);
        $this->get($printUrl)->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('nota.show', $nota))
            ->assertOk()
            ->assertSee($printUrl, false);
        $this->get($printUrl)->assertOk();
    }
}
