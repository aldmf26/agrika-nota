<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaIssue;
use App\Models\WeeklyReview;
use App\Services\WeeklyReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyReviewController extends Controller
{
    public function __construct(private WeeklyReviewService $service) {}

    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $weeks = collect(range(1, 4))->mapWithKeys(fn ($week) => [$week => $this->service->summary($year, $month, $week)]);

        return view('weekly-reviews.index', compact('year', 'month', 'weeks'));
    }

    public function show(int $year, int $month, int $week)
    {
        abort_unless($month >= 1 && $month <= 12 && $week >= 1 && $week <= 4, 404);
        $summary = $this->service->summary($year, $month, $week);
        [$start, $end] = $this->service->range($year, $month, $week);
        $replacementCandidates = auth()->user()->hasRole('super_admin')
            ? Nota::where('status', 'approved')->orderByDesc('tanggal_nota')->limit(100)->get()
            : collect();

        return view('weekly-reviews.show', compact('year', 'month', 'week', 'summary', 'start', 'end', 'replacementCandidates'));
    }

    public function close(int $year, int $month, int $week)
    {
        $summary = $this->service->summary($year, $month, $week);
        abort_if($summary['openIssues'] > 0, 422, 'Selesaikan semua laporan masalah sebelum menutup minggu.');

        DB::transaction(function () use ($year, $month, $week, $summary) {
            $review = WeeklyReview::updateOrCreate(compact('year', 'month', 'week'), [
                'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'nota_ids' => $summary['ids'],
                'nota_count' => $summary['count'], 'total_nominal' => $summary['total'],
            ]);
            $review->snapshots()->create([
                'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'nota_ids' => $summary['ids'],
                'nota_count' => $summary['count'], 'total_nominal' => $summary['total'],
            ]);
        });

        return back()->with('success', 'Minggu berhasil ditandai sudah diperiksa.');
    }

    public function report(Request $request, Nota $nota)
    {
        abort_unless($nota->status === 'approved', 422);
        $data = $request->validate(['note' => ['required', 'string', 'min:5', 'max:500']]);
        NotaIssue::create(['nota_id' => $nota->id, 'reported_by' => auth()->id(), 'reported_at' => now(), 'note' => $data['note']]);

        return back()->with('success', 'Masalah ditandai. Teks laporan siap disalin.');
    }

    public function resolve(Request $request, NotaIssue $issue)
    {
        $data = $request->validate(['replacement_nota_id' => ['required', 'exists:notas,id', 'different:nota_id']]);
        abort_unless($issue->nota->status === 'void', 422, 'Nota lama harus di-void terlebih dahulu.');
        $replacement = Nota::findOrFail($data['replacement_nota_id']);
        abort_unless($replacement->status === 'approved', 422, 'Nota pengganti harus approved.');
        $issue->update(['replacement_nota_id' => $replacement->id, 'resolved_by' => auth()->id(), 'resolved_at' => now()]);

        return back()->with('success', 'Laporan masalah diselesaikan.');
    }
}
