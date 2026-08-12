<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateNotaAttachmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Divisi $divisi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['nota.create', 'nota.view-own']);
        $this->divisi = Divisi::create([
            'nama' => 'Divisi Test',
            'aktif' => true,
        ]);
    }

    public function test_nota_can_be_created_without_attachment(): void
    {
        $response = $this->actingAs($this->user)->post(route('nota.store'), $this->validNotaData());

        $nota = Nota::whereBelongsTo($this->user)->firstOrFail();

        $response->assertRedirect(route('nota.show', $nota));
        $this->assertSame('pending', $nota->status);
        $this->assertCount(0, $nota->attachments);
        $this->actingAs($this->user)->get(route('nota.show', $nota))->assertOk();
    }

    public function test_valid_image_attachment_is_still_accepted(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post(route('nota.store'), array_merge(
            $this->validNotaData(),
            ['attachments' => [UploadedFile::fake()->image('bukti.jpg')]],
        ));

        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Nota::whereBelongsTo($this->user)->firstOrFail()->attachments);
    }

    public function test_non_image_attachment_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('nota.store'), array_merge(
            $this->validNotaData(),
            ['attachments' => [UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')]],
        ));

        $response->assertSessionHasErrors('attachments.0');
        $this->assertDatabaseCount('notas', 0);
    }

    public function test_more_than_twenty_attachments_are_rejected(): void
    {
        $attachments = array_map(
            fn (int $index) => UploadedFile::fake()->image("bukti-{$index}.jpg"),
            range(1, 21),
        );

        $response = $this->actingAs($this->user)->post(route('nota.store'), array_merge(
            $this->validNotaData(),
            ['attachments' => $attachments],
        ));

        $response->assertSessionHasErrors('attachments');
        $this->assertDatabaseCount('notas', 0);
    }

    public function test_attachment_larger_than_five_megabytes_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('nota.store'), array_merge(
            $this->validNotaData(),
            ['attachments' => [UploadedFile::fake()->image('besar.jpg')->size(5121)]],
        ));

        $response->assertSessionHasErrors('attachments.0');
        $this->assertDatabaseCount('notas', 0);
    }

    private function validNotaData(): array
    {
        return [
            'tipe' => 'biasa',
            'tanggal_nota' => now()->toDateString(),
            'divisi_id' => $this->divisi->id,
            'nomor_nota' => 'TEST-001',
            'keterangan' => 'Biaya operasional test',
            'nominal' => 10000,
        ];
    }
}
