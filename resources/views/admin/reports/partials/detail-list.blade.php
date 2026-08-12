<div class="border-b border-slate-100 px-4 py-4 sm:px-6">
    <p class="text-xs font-bold uppercase text-slate-500">Detail Laporan</p>
    <h2 class="mt-1 text-lg font-bold text-slate-900 sm:text-xl">{{ $title }}</h2>
    <div class="mt-3 flex items-center justify-between gap-4">
        <span class="text-sm text-slate-500">{{ $notas->count() }} nota approved</span>
        <span class="text-lg font-black text-indigo-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
    </div>
</div>

<div class="max-h-[65vh] divide-y divide-slate-100 overflow-y-auto">
    @forelse($notas as $nota)
        <a href="{{ route('nota.show', $nota) }}" class="block px-4 py-4 transition-colors hover:bg-slate-50 sm:px-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-slate-900">{{ $nota->nomor_nota ?? '(digital)' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $nota->tanggal_nota->format('d M Y') }} · {{ ucfirst(str_replace('_', ' ', $nota->tipe)) }}</p>
                </div>
                <p class="shrink-0 text-sm font-bold text-slate-900">Rp {{ number_format($nota->report_amount, 0, ',', '.') }}</p>
            </div>
            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $nota->keterangan }}</p>
            <div class="mt-2 flex items-center justify-between gap-3 text-xs text-slate-400">
                <span class="truncate">{{ $nota->user->name ?? '-' }}</span>
                <span class="font-semibold text-green-600">Lihat nota →</span>
            </div>
        </a>
    @empty
        <div class="px-4 py-12 text-center sm:px-6">
            <p class="font-bold text-slate-900">Tidak ada nota ditemukan</p>
            <p class="mt-1 text-sm text-slate-500">Data rekap untuk pilihan ini kosong.</p>
        </div>
    @endforelse
</div>
