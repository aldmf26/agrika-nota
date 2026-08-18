<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\NotaAttachment;
use App\Models\SystemBackup;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemResetBackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_only_super_admin_can_create_and_download_reset_backup(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->post(route('admin.system.backups.create'))->assertForbidden();

        $this->actingAs($this->superAdmin)
            ->post(route('admin.system.backups.create'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $backup = SystemBackup::firstOrFail();
        Storage::disk('local')->assertExists($backup->path);
        $this->get(route('admin.system.backups.download', $backup))->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.system.backups.download', $backup))
            ->assertForbidden();
    }

    public function test_backup_json_is_readable_and_contains_metadata_but_not_photo_content(): void
    {
        $nota = Nota::factory()->pending()->create(['user_id' => $this->superAdmin->id]);
        Storage::disk('public')->put('nota/test/bukti.jpg', 'ISI-FOTO-RAHASIA');
        NotaAttachment::create([
            'nota_id' => $nota->id,
            'file_name' => 'bukti.jpg',
            'file_path' => 'nota/test/bukti.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 18,
        ]);

        $this->actingAs($this->superAdmin)->post(route('admin.system.backups.create'));
        $backup = SystemBackup::firstOrFail();
        $json = Storage::disk('local')->get($backup->path);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('agrika-nota-reset-backup', $decoded['metadata']['format']);
        $this->assertFalse($decoded['metadata']['photos_included']);
        $this->assertSame('bukti.jpg', $decoded['data']['notas'][0]['attachments'][0]['file_name']);
        $this->assertFalse($decoded['data']['notas'][0]['attachments'][0]['file_included']);
        $this->assertStringNotContainsString('ISI-FOTO-RAHASIA', $json);
        $this->assertSame(hash('sha256', $json), $backup->checksum);
    }

    public function test_reset_requires_exact_confirmation_and_fresh_backup(): void
    {
        Nota::factory()->pending()->create(['user_id' => $this->superAdmin->id]);
        $this->actingAs($this->superAdmin)->post(route('admin.system.backups.create'));
        $backup = SystemBackup::firstOrFail();

        $this->post(route('admin.system.reset'), ['backup_id' => $backup->id, 'confirmation' => 'reset'])
            ->assertSessionHasErrors('confirmation');
        $this->assertDatabaseCount('notas', 1);

        Nota::factory()->pending()->create(['user_id' => $this->superAdmin->id]);
        $this->post(route('admin.system.reset'), ['backup_id' => $backup->id, 'confirmation' => 'RESET'])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('notas', 2);
    }

    public function test_valid_backup_allows_reset_and_keeps_accounts_divisions_and_backup(): void
    {
        $division = Divisi::create(['kode' => 'TST', 'nama' => 'Divisi Test', 'aktif' => true]);
        $nota = Nota::factory()->pending()->create([
            'user_id' => $this->superAdmin->id,
            'divisi_id' => $division->id,
        ]);
        Storage::disk('public')->put("nota/{$nota->id}/bukti.jpg", 'foto');

        $this->actingAs($this->superAdmin)->post(route('admin.system.backups.create'));
        $backup = SystemBackup::firstOrFail();
        $this->post(route('admin.system.reset'), [
            'backup_id' => $backup->id,
            'confirmation' => 'RESET',
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('notas', 0);
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
        $this->assertDatabaseHas('divisis', ['id' => $division->id]);
        $this->assertDatabaseHas('system_backups', ['id' => $backup->id, 'status' => 'used']);
        Storage::disk('local')->assertExists($backup->path);
        Storage::disk('public')->assertMissing('nota');
    }

    public function test_prune_command_deletes_expired_backup_file_and_record(): void
    {
        $this->actingAs($this->superAdmin)->post(route('admin.system.backups.create'));
        $backup = SystemBackup::firstOrFail();
        $backup->update(['expires_at' => now()->subMinute()]);

        $this->artisan('system-backups:prune')->assertSuccessful();

        Storage::disk('local')->assertMissing($backup->path);
        $this->assertDatabaseMissing('system_backups', ['id' => $backup->id]);
    }
}
