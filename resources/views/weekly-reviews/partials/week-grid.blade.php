@php
    $labels = ['belum_diperiksa' => ['Belum Diperiksa', 'bg-amber-50 text-amber-700'], 'sudah_diperiksa' => ['Sudah Diperiksa', 'bg-green-50 text-green-700'], 'ada_tambahan' => ['Ada Tambahan', 'bg-blue-50 text-blue-700'], 'bermasalah' => ['Bermasalah', 'bg-red-50 text-red-700']];
@endphp
<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
@foreach($weeks as $number => $summary)
    @php [$start, $end] = app(\App\Services\WeeklyReviewService::class)->range($year, $month, $number); @endphp
    <a href="{{ route('weekly-reviews.show', [$year, $month, $number]) }}" class="block border border-slate-200 bg-white p-4 shadow-sm hover:border-green-400 sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div><p class="text-lg font-bold text-slate-900">Minggu {{ $number }}</p><p class="text-xs text-slate-500">{{ $start->format('d') }}–{{ $end->format('d M Y') }}</p></div>
            <span class="px-2 py-1 text-xs font-bold {{ $labels[$summary['status']][1] }}">{{ $labels[$summary['status']][0] }}</span>
        </div>
        <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-sm">
            <div><p class="text-xs text-slate-500">Nota</p><p class="font-bold">{{ $summary['count'] }}</p></div>
            <div><p class="text-xs text-slate-500">Divisi</p><p class="font-bold">{{ $summary['divisions'] }}</p></div>
            <div class="text-right"><p class="text-xs text-slate-500">Total</p><p class="font-bold">Rp {{ number_format($summary['total'], 0, ',', '.') }}</p></div>
        </div>
    </a>
@endforeach
</div>
