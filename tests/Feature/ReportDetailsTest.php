<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_drill_down_report_amount_for_regular_and_split_notas(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $divisi = Divisi::create(['nama' => 'Aga', 'aktif' => true]);
        $otherDivisi = Divisi::create(['nama' => 'Takemori', 'aktif' => true]);

        Nota::create([
            'user_id' => $user->id,
            'divisi_id' => $divisi->id,
            'tipe' => 'biasa',
            'status' => 'approved',
            'nomor_nota' => 'REG-001',
            'keterangan' => 'Nota reguler laporan',
            'tanggal_nota' => '2026-01-10',
            'tahun' => 2026,
            'bulan' => 1,
            'nominal' => 100000,
        ]);

        $split = Nota::create([
            'user_id' => $user->id,
            'divisi_id' => $divisi->id,
            'tipe' => 'split',
            'status' => 'approved',
            'nomor_nota' => 'SPLIT-001',
            'keterangan' => 'Nota split laporan',
            'tanggal_nota' => '2026-01-12',
            'tahun' => 2026,
            'bulan' => 1,
            'nominal' => 300000,
        ]);
        $split->items()->createMany([
            ['divisi_id' => $divisi->id, 'nominal' => 200000],
            ['divisi_id' => $otherDivisi->id, 'nominal' => 100000],
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.details', [
            'tahun' => 2026,
            'bulan' => 1,
            'divisi_id' => $divisi->id,
        ]));

        $response->assertOk()
            ->assertSee('REG-001')
            ->assertSee('SPLIT-001')
            ->assertSee('Rp 100.000')
            ->assertSee('Rp 200.000')
            ->assertSee('Rp 300.000');
    }

    public function test_non_super_admin_cannot_access_report_details(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('admin.reports.details', ['tahun' => 2026]))->assertForbidden();
    }
}
