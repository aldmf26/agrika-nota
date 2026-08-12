<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Nota;
use App\Models\NotaIssue;
use App\Models\User;
use App\Models\WeeklyReview;
use App\Services\WeeklyReviewService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $approver;

    private User $superAdmin;

    private User $admin;

    private Divisi $divisi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->approver = User::factory()->create();
        $this->approver->assignRole('approver');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->divisi = Divisi::create(['nama' => 'Aga', 'kode' => 'AGA', 'aktif' => true]);
    }

    public function test_days_are_grouped_into_four_fixed_weeks(): void
    {
        $service = app(WeeklyReviewService::class);
        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], collect([1, 7, 8, 14, 15, 21, 22, 31])->map(fn ($day) => $service->weekForDay($day))->all());
    }

    public function test_approver_dashboard_only_shows_weekly_work_and_cannot_approve(): void
    {
        $pending = Nota::factory()->pending()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id]);

        $this->actingAs($this->approver)->get(route('dashboard'))->assertOk()->assertSee('Pemeriksaan Mingguan')->assertDontSee('Pending Approval');
        $this->actingAs($this->approver)->post(route('nota.approve', $pending))->assertForbidden();
        $this->actingAs($this->superAdmin)->post(route('nota.approve', $pending))->assertRedirect();
    }

    public function test_approver_history_only_contains_approved_notas(): void
    {
        Nota::factory()->pending()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'nomor_nota' => 'PENDING-HIDDEN']);
        Nota::factory()->approved()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'nomor_nota' => 'APPROVED-VISIBLE']);

        $this->actingAs($this->approver)->get(route('nota.index'))->assertOk()->assertSee('APPROVED-VISIBLE')->assertDontSee('PENDING-HIDDEN');
    }

    public function test_closing_week_saves_snapshot_and_late_nota_marks_additional_review(): void
    {
        Nota::factory()->approved()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'tanggal_nota' => '2026-08-05', 'tahun' => 2026, 'bulan' => 8]);
        $this->actingAs($this->approver)->post(route('weekly-reviews.close', [2026, 8, 1]))->assertRedirect();

        $review = WeeklyReview::firstOrFail();
        $this->assertSame(1, $review->nota_count);
        $this->assertCount(1, $review->snapshots);

        Nota::factory()->approved()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'tanggal_nota' => '2026-08-06', 'tahun' => 2026, 'bulan' => 8]);
        $this->actingAs($this->approver)->get(route('weekly-reviews.index', ['year' => 2026, 'month' => 8]))->assertSee('Ada Tambahan');
        $this->actingAs($this->approver)->post(route('weekly-reviews.close', [2026, 8, 1]))->assertRedirect();
        $this->assertCount(2, $review->fresh()->snapshots);
    }

    public function test_issue_blocks_closing_until_void_nota_is_linked_to_approved_replacement(): void
    {
        $nota = Nota::factory()->approved()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'tanggal_nota' => '2026-08-10', 'tahun' => 2026, 'bulan' => 8]);
        $this->actingAs($this->approver)->post(route('weekly-reviews.issues.store', $nota), ['note' => 'Nominal tidak sesuai'])->assertRedirect();
        $this->actingAs($this->approver)->post(route('weekly-reviews.close', [2026, 8, 2]))->assertUnprocessable();

        $nota->update(['status' => 'void']);
        $replacement = Nota::factory()->approved()->create(['user_id' => $this->admin->id, 'divisi_id' => $this->divisi->id, 'tanggal_nota' => '2026-08-10', 'tahun' => 2026, 'bulan' => 8]);
        $issue = NotaIssue::firstOrFail();
        $this->actingAs($this->superAdmin)->post(route('admin.weekly-review-issues.resolve', $issue), ['replacement_nota_id' => $replacement->id])->assertRedirect();
        $this->assertNotNull($issue->fresh()->resolved_at);
        $this->actingAs($this->approver)->post(route('weekly-reviews.close', [2026, 8, 2]))->assertRedirect();
    }
}
