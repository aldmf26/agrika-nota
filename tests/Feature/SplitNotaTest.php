<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitNotaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Divisi $aga;

    private Divisi $agri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->aga = Divisi::create(['nama' => 'Aga', 'kode' => 'AGA', 'aktif' => true]);
        $this->agri = Divisi::create(['nama' => 'Agri', 'kode' => 'AGR', 'aktif' => true]);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'tipe' => 'split',
            'tanggal_nota' => now()->toDateString(),
            'nomor_nota' => '0001',
            'keterangan' => 'Tagihan bersama kantor',
            'nominal_total' => 500000,
            'split_mode' => 'rupiah',
            'split_items' => [
                ['divisi_id' => $this->aga->id, 'nominal' => 300000],
                ['divisi_id' => $this->agri->id, 'nominal' => 200000],
            ],
        ], $overrides);
    }

    public function test_create_form_renders_split_editor(): void
    {
        $this->actingAs($this->admin)
            ->get(route('nota.create'))
            ->assertOk()
            ->assertSee('Mode Pembagian')
            ->assertSee('splitItemsData', false);
    }

    public function test_rupiah_split_must_equal_total_and_uses_spl_prefix(): void
    {
        $this->actingAs($this->admin)->post(route('nota.store'), $this->payload())->assertRedirect();

        $nota = Nota::firstOrFail();
        $this->assertSame('SPL0001', $nota->nomor_nota);
        $this->assertNull($nota->divisi_id);
        $this->assertSame(500000, $nota->nominal);
        $this->assertSame(500000, $nota->items->sum('nominal'));
    }

    public function test_rupiah_split_rejects_under_over_and_duplicate_divisions(): void
    {
        foreach ([499000, 600000] as $secondNominal) {
            $payload = $this->payload();
            $payload['split_items'][1]['nominal'] = $secondNominal;
            $this->actingAs($this->admin)->post(route('nota.store'), $payload)->assertSessionHasErrors('split_items');
        }

        $payload = $this->payload();
        $payload['split_items'][1] = ['divisi_id' => $this->aga->id, 'nominal' => 200000];
        $this->actingAs($this->admin)->post(route('nota.store'), $payload)->assertSessionHasErrors('split_items.1.divisi_id');
        $this->assertDatabaseCount('notas', 0);
    }

    public function test_percentage_split_requires_one_hundred_percent_and_corrects_rounding_on_last_item(): void
    {
        $payload = $this->payload([
            'nominal_total' => 100001,
            'split_mode' => 'persen',
            'split_items' => [
                ['divisi_id' => $this->aga->id, 'persentase' => 33.33],
                ['divisi_id' => $this->agri->id, 'persentase' => 66.67],
            ],
        ]);
        $this->actingAs($this->admin)->post(route('nota.store'), $payload)->assertRedirect();

        $nota = Nota::firstOrFail();
        $this->assertSame(100001, $nota->items->sum('nominal'));
        $this->assertSame(66671, $nota->items->last()->nominal);

        $invalid = $payload;
        $invalid['nomor_nota'] = '0002';
        $invalid['split_items'][1]['persentase'] = 60;
        $this->actingAs($this->admin)->post(route('nota.store'), $invalid)->assertSessionHasErrors('split_items');
    }

    public function test_non_split_still_requires_main_division(): void
    {
        $payload = $this->payload(['tipe' => 'biasa', 'nominal' => 500000]);
        unset($payload['divisi_id'], $payload['split_items'], $payload['nominal_total'], $payload['split_mode']);

        $this->actingAs($this->admin)->post(route('nota.store'), $payload)->assertSessionHasErrors('divisi_id');
    }
}
