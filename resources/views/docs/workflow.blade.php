@extends('layouts.app')

@section('title', 'Alur dan Peran')

@section('content')
    <div class="mx-auto max-w-5xl py-4 sm:py-10">
        <header class="mb-7 max-w-2xl">
            <p class="text-sm font-bold uppercase text-emerald-700">Alur kerja</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Peran dan pemeriksaan nota</h1>
            <p class="mt-3 text-slate-600">Siapa mengerjakan apa, dari pengajuan sampai pemeriksaan mingguan.</p>
        </header>

        @include('docs.partials.navigation')

        <section class="grid gap-3 md:grid-cols-3">
            @foreach ([
                ['1', 'Admin', 'Membuat nota, memperbaiki pending/rejected, dan melihat history miliknya.'],
                ['2', 'Super Admin / IT', 'Menyetujui, menolak, atau void nota serta mengelola data sistem.'],
                ['3', 'Pemeriksa', 'Memeriksa nota approved per minggu dan melaporkan masalah.'],
            ] as [$number, $role, $task])
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <span class="text-xs font-black text-emerald-700">LANGKAH {{ $number }}</span>
                    <h2 class="mt-2 text-lg font-bold text-slate-900">{{ $role }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $task }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-7 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-bold text-slate-900">Arti status nota</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr><th class="px-5 py-3">Status</th><th class="px-5 py-3">Arti</th><th class="px-5 py-3">Tindakan</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        <tr><td class="px-5 py-3"><x-status-badge status="pending" /></td><td class="px-5 py-3">Menunggu keputusan IT</td><td class="px-5 py-3">Admin masih dapat mengedit</td></tr>
                        <tr><td class="px-5 py-3"><x-status-badge status="approved" /></td><td class="px-5 py-3">Disetujui IT</td><td class="px-5 py-3">Masuk pemeriksaan mingguan</td></tr>
                        <tr><td class="px-5 py-3"><x-status-badge status="rejected" /></td><td class="px-5 py-3">Perlu diperbaiki</td><td class="px-5 py-3">Admin edit dan kirim ulang</td></tr>
                        <tr><td class="px-5 py-3"><x-status-badge status="void" /></td><td class="px-5 py-3">Nota dibatalkan</td><td class="px-5 py-3">Riwayat tetap tersimpan</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-7 grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Pemeriksaan mingguan</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Periode dibagi 1-7, 8-14, 15-21, dan 22-akhir bulan. Minggu dapat ditutup setelah semua nota benar.</p>
                <p class="mt-3 text-sm leading-6 text-slate-600"><strong>Ada Tambahan</strong> berarti nota terlambat masuk dan minggu perlu diperiksa ulang.</p>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-5">
                <h2 class="font-bold text-red-900">Jika nota bermasalah</h2>
                <p class="mt-2 text-sm leading-6 text-red-800">Pemeriksa menulis catatan dan menyalin laporan. IT melakukan void, admin membuat nota pengganti, lalu IT menghubungkan pengganti dan menyelesaikan masalah.</p>
            </div>
        </section>
    </div>
@endsection
