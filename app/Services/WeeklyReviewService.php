<?php

namespace App\Services;

use App\Models\Nota;
use App\Models\NotaIssue;
use App\Models\WeeklyReview;
use Carbon\Carbon;

class WeeklyReviewService
{
    public function weekForDay(int $day): int
    {
        return match (true) {
            $day <= 7 => 1,
            $day <= 14 => 2,
            $day <= 21 => 3,
            default => 4,
        };
    }

    public function range(int $year, int $month, int $week): array
    {
        $startDay = [1 => 1, 2 => 8, 3 => 15, 4 => 22][$week];
        $endDay = $week === 4 ? Carbon::create($year, $month)->endOfMonth()->day : $startDay + 6;

        return [Carbon::create($year, $month, $startDay)->startOfDay(), Carbon::create($year, $month, $endDay)->endOfDay()];
    }

    public function notas(int $year, int $month, int $week)
    {
        [$start, $end] = $this->range($year, $month, $week);

        return Nota::with(['user', 'divisi', 'items.divisi', 'attachments', 'issues.replacement'])
            ->where('status', 'approved')
            ->whereBetween('tanggal_nota', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal_nota')->get();
    }

    public function summary(int $year, int $month, int $week): array
    {
        $notas = $this->notas($year, $month, $week);
        $review = WeeklyReview::where(compact('year', 'month', 'week'))->first();
        $ids = $notas->pluck('id')->sort()->values()->all();
        [$start, $end] = $this->range($year, $month, $week);
        $issues = NotaIssue::with(['nota.user', 'replacement'])
            ->whereNull('resolved_at')
            ->whereHas('nota', fn ($query) => $query->whereBetween('tanggal_nota', [$start->toDateString(), $end->toDateString()]))
            ->get();
        $openIssues = $issues->count();
        $changed = $review?->reviewed_at && ($review->nota_ids ?? []) !== $ids;
        $status = $openIssues ? 'bermasalah' : ($changed ? 'ada_tambahan' : ($review?->reviewed_at ? 'sudah_diperiksa' : 'belum_diperiksa'));

        return compact('notas', 'review', 'ids', 'issues', 'openIssues', 'status') + [
            'count' => count($ids),
            'total' => $notas->sum(fn ($nota) => $nota->getNominalTotal()),
            'divisions' => $notas->flatMap(fn ($nota) => $nota->getDivisiTerlibat()->pluck('id'))->unique()->count(),
        ];
    }
}
