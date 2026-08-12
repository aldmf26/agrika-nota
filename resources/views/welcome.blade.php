@extends('layouts.app')

@section('title', 'Portal Internal')

@section('content')
    <div class="mx-auto max-w-6xl py-4 sm:py-10">
        <section
            class="grid items-center gap-8 border-b border-slate-200 py-8 lg:grid-cols-[1.05fr_.95fr] lg:py-12">
            <div class="max-w-2xl">
                <p class="mb-3 text-sm font-bold uppercase text-emerald-700">Sistem pencatatan nota internal</p>
                <h1 class="text-4xl font-black leading-tight text-slate-950 sm:text-5xl">Agrika Nota</h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                    Pencatatan, persetujuan, dan pemeriksaan nota mingguan dalam satu sistem internal.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">
                            Buka Dashboard
                        </a>
                        @can('create', App\Models\Nota::class)
                            <a href="{{ route('nota.create') }}"
                                class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                Buat Nota
                            </a>
                        @endcan
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">
                            Masuk
                        </a>
                    @endauth
                    <a href="{{ route('docs.index') }}"
                        class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Buka Panduan
                    </a>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Alur kerja digital</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ([['01', 'Admin mencatat nota', 'Data transaksi dan bukti pendukung tersimpan terpusat.'], ['02', 'IT memeriksa keputusan', 'Super admin menyetujui atau menolak nota yang diajukan.'], ['03', 'Pemeriksaan mingguan', 'Nota approved ditinjau kembali per periode oleh pemeriksa.']] as [$number, $title, $description])
                        <div class="flex gap-4 p-5">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-xs font-black text-emerald-700">{{ $number }}</span>
                            <div>
                                <h2 class="font-bold text-slate-900">{{ $title }}</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-10 sm:py-12">
            <div class="mb-6 max-w-2xl">
                <h2 class="text-2xl font-black text-slate-900">Akses cepat</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Pilih halaman sesuai pekerjaan Anda.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase text-emerald-700">Nota</p>
                    <h3 class="mt-2 text-lg font-bold text-slate-900">Buat dan cari nota</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Kelola nota biasa, split, lampiran, dan history.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase text-amber-700">Laporan</p>
                    <h3 class="mt-2 text-lg font-bold text-slate-900">Pantau pengeluaran</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Lihat rekap bulanan, detail transaksi, dan export.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase text-slate-600">Panduan</p>
                    <h3 class="mt-2 text-lg font-bold text-slate-900">Pelajari alur kerja</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Baca tugas tiap peran dan arti status nota.</p>
                </article>
            </div>
        </section>
    </div>
@endsection
