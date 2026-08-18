<?php

namespace Tests\Feature;

use App\Models\Nota;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_super_admin_can_toggle_print_qr_setting()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Turn off QR Code
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.update'), [
            // no enable_print_qr field means turn off
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertFalse(SystemSetting::isQrEnabled());

        // Turn on QR Code
        $response = $this->actingAs($superAdmin)->post(route('admin.settings.update'), [
            'enable_print_qr' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertTrue(SystemSetting::isQrEnabled());
    }

    public function test_non_super_admin_cannot_access_system_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));
        $response->assertStatus(403);
    }
}
