@extends('layouts.app')
@section('title', 'Pemeriksaan Minggu ' . $week)
@section('content')
<div class="mx-auto max-w-5xl py-3 sm:py-8">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><a href="{{ route('weekly-reviews.index', ['year'=>$year,'month'=>$month]) }}" class="text-sm font-semibold text-green-700">Kembali</a><h1 class="mt-2 text-2xl font-bold text-slate-900">Minggu {{ $week }}</h1><p class="text-sm text-slate-500">{{ $start->format('d') }}–{{ $end->format('d F Y') }} · {{ $summary['count'] }} nota · Rp {{ number_format($summary['total'],0,',','.') }}</p></div>
        @if(auth()->user()->can('weekly-review.close'))
        <form method="POST" action="{{ route('weekly-reviews.close', [$year,$month,$week]) }}">@csrf
            <button @disabled($summary['openIssues'] > 0) class="w-full bg-green-600 px-5 py-3 text-sm font-bold text-white disabled:cursor-not-allowed disabled:bg-slate-300">{{ $summary['review']?->reviewed_at ? 'Periksa & Tutup Ulang' : 'Tandai Sudah Diperiksa' }}</button>
        </form>
        @endif
    </div>

    @if($summary['openIssues'])<div class="mb-4 border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $summary['openIssues'] }} masalah belum selesai. Minggu belum dapat ditutup.</div>@endif

    @foreach($summary['issues']->filter(fn($issue) => $issue->nota->status !== 'approved') as $openIssue)
        <div class="mb-3 border border-red-300 bg-white p-4">
            <p class="font-bold text-red-700">{{ $openIssue->nota->nomor_nota }} · {{ strtoupper($openIssue->nota->status) }}</p>
            <p class="mt-1 text-sm text-slate-700">{{ $openIssue->note }}</p>
            @if(auth()->user()->hasRole('super_admin'))
                <form method="POST" action="{{ route('admin.weekly-review-issues.resolve',$openIssue) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">@csrf
                    <select name="replacement_nota_id" required class="min-w-0 flex-1 border border-slate-200 px-3 py-2 text-sm"><option value="">Pilih nota pengganti approved</option>@foreach($replacementCandidates->where('id','!=',$openIssue->nota_id) as $candidate)<option value="{{ $candidate->id }}">{{ $candidate->nomor_nota }} · {{ $candidate->tanggal_nota->format('d/m/Y') }}</option>@endforeach</select>
                    <button class="bg-slate-900 px-3 py-2 text-sm font-bold text-white">Hubungkan Pengganti</button>
                </form>
            @endif
        </div>
    @endforeach

    <div class="space-y-3">
    @forelse($summary['notas'] as $nota)
        @php $openIssue = $nota->issues->firstWhere('resolved_at', null); @endphp
        <article class="border {{ $openIssue ? 'border-red-300' : 'border-slate-200' }} bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><a href="{{ route('nota.show',$nota) }}" class="font-bold text-slate-900 hover:text-green-700">{{ $nota->nomor_nota ?? '(Digital)' }}</a><p class="mt-1 text-xs text-slate-500">{{ $nota->tanggal_nota->format('d M Y') }} · {{ $nota->user->name }} · {{ $nota->divisi->nama ?? $nota->items->pluck('divisi.nama')->filter()->join(', ') }}</p></div>
                <p class="shrink-0 font-bold text-slate-900">{{ $nota->nominal_formatted }}</p>
            </div>
            <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('nota.show',$nota) }}" class="bg-slate-100 px-3 py-2 text-center text-sm font-semibold text-slate-700">Lihat Detail & Foto</a>
                @if(auth()->user()->can('weekly-review.report-issue') && !$openIssue)
                    <form method="POST" action="{{ route('weekly-reviews.issues.store',$nota) }}" class="flex flex-1 flex-col gap-2 sm:flex-row" onsubmit="copyIssueReport(this)">@csrf
                        <input name="note" required minlength="5" maxlength="500" placeholder="Tulis masalah nota" class="min-w-0 flex-1 border border-slate-200 px-3 py-2 text-sm">
                        <button class="bg-red-600 px-3 py-2 text-sm font-bold text-white">Salin Laporan Masalah</button>
                    </form>
                @endif
            </div>
            @if($openIssue)
                <div class="mt-3 border-t border-red-100 pt-3"><p class="text-sm font-bold text-red-700">Bermasalah: {{ $openIssue->note }}</p>
                @if(auth()->user()->hasRole('super_admin'))
                    <form method="POST" action="{{ route('admin.weekly-review-issues.resolve',$openIssue) }}" class="mt-2 flex flex-col gap-2 sm:flex-row">@csrf
                        <select name="replacement_nota_id" required class="min-w-0 flex-1 border border-slate-200 px-3 py-2 text-sm"><option value="">Pilih nota pengganti approved</option>@foreach($replacementCandidates->where('id','!=',$nota->id) as $candidate)<option value="{{ $candidate->id }}">{{ $candidate->nomor_nota }} · {{ $candidate->tanggal_nota->format('d/m/Y') }}</option>@endforeach</select>
                        <button class="bg-slate-900 px-3 py-2 text-sm font-bold text-white">Hubungkan Pengganti</button>
                    </form>
                    <p class="mt-1 text-xs text-slate-500">Void nota lama terlebih dahulu.</p>
                @endif</div>
            @endif
        </article>
    @empty
        <div class="border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">Belum ada nota approved pada minggu ini.</div>
    @endforelse
    </div>
</div>
<script>
function copyIssueReport(form) {
    const nota = @json('');
    const card = form.closest('article');
    const number = card.querySelector('a').textContent.trim();
    const note = form.querySelector('[name="note"]').value;
    const text = `Nota ${number} perlu diperiksa ulang.\nMinggu {{ $week }}, {{ $start->format('d/m/Y') }}–{{ $end->format('d/m/Y') }}.\nMasalah: ${note}\nLink: ${card.querySelector('a').href}`;
    navigator.clipboard?.writeText(text);
}
</script>
@endsection
