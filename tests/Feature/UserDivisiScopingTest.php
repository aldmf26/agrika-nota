<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDivisiScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super_admin']);
    }

    public function test_admin_with_specific_divisi_can_only_see_their_assigned_divisi()
    {
        $d1 = Divisi::create(['nama' => 'Takemori', 'kode' => 'TKM', 'aktif' => true]);
        $d2 = Divisi::create(['nama' => 'Soondobu', 'kode' => 'SDB', 'aktif' => true]);
        $d3 = Divisi::create(['nama' => 'Estate', 'kode' => 'EST', 'aktif' => true]);

        $admin = User::factory()->create(['all_divisi' => false]);
        $admin->assignRole('admin');
        $admin->divisis()->attach([$d1->id, $d2->id]);

        $accessible = $admin->accessibleDivisis();
        $accessibleIds = $accessible->pluck('id')->toArray();

        $this->assertContains($d1->id, $accessibleIds);
        $this->assertContains($d2->id, $accessibleIds);
        $this->assertNotContains($d3->id, $accessibleIds);

        $this->assertTrue($admin->canAccessDivisi($d1->id));
        $this->assertFalse($admin->canAccessDivisi($d3->id));
    }

    public function test_global_admin_can_access_all_divisis()
    {
        $d1 = Divisi::create(['nama' => 'Takemori', 'kode' => 'TKM', 'aktif' => true]);
        $d2 = Divisi::create(['nama' => 'Estate', 'kode' => 'EST', 'aktif' => true]);

        $admin = User::factory()->create(['all_divisi' => true]);
        $admin->assignRole('admin');

        $accessible = $admin->accessibleDivisis();
        $this->assertCount(2, $accessible);
        $this->assertTrue($admin->canAccessDivisi($d1->id));
        $this->assertTrue($admin->canAccessDivisi($d2->id));
    }
}
