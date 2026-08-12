@extends('layouts.app')

@section('title', 'Cara Menggunakan')

@section('content')
    <div class="mx-auto max-w-5xl py-4 sm:py-10">
        <header class="mb-7 max-w-2xl">
            <p class="text-sm font-bold uppercase text-emerald-700">Panduan pengguna</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Cara menggunakan Agrika Nota</h1>
            <p class="mt-3 text-slate-600">Langkah penting tanpa penjelasan panjang.</p>
        </header>

        @include('docs.partials.navigation')

        <div class="grid gap-4 lg:grid-cols-2">
            <section id="buat-nota" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-sm font-black text-emerald-700">1</span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Buat nota</h2>
                <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                    <li>Pilih tipe, tanggal, dan divisi.</li>
                    <li>Isi keterangan dan nominal.</li>
                    <li>Unggah gambar bila ada, maksimal 20 file dan 5 MB per file.</li>
                    <li>Klik <strong>Simpan</strong>. Nota masuk status pending.</li>
                </ol>
            </section>

            <section id="split" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-sm font-black text-emerald-700">2</span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Buat nota split</h2>
                <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                    <li>Pilih tipe <strong>Split</strong> dan isi nominal total.</li>
                    <li>Pilih mode Rupiah atau Persen.</li>
                    <li>Tambahkan 2-20 divisi tanpa duplikat.</li>
                    <li>Pastikan alokasi sama dengan total atau tepat 100%.</li>
                </ol>
            </section>

            <section id="history" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-sm font-black text-slate-600">3</span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Cari dan koreksi nota</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                    <li>Gunakan History untuk pencarian, status, tipe, divisi, dan periode.</li>
                    <li>Nota pending milik sendiri masih dapat diedit.</li>
                    <li>Nota rejected dapat diperbaiki lalu dikirim ulang.</li>
                    <li>Nota approved dan void tidak dapat diedit.</li>
                </ul>
            </section>

            <section id="laporan" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-sm font-black text-slate-600">4</span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Lihat laporan dan cetak</h2>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                    <li>Klik nominal laporan untuk melihat nota penyusunnya.</li>
                    <li>Gunakan Export untuk rekap atau detail Excel.</li>
                    <li>Buka detail nota lalu pilih Cetak untuk dokumen transaksi.</li>
                </ul>
            </section>
        </div>

        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
            Periksa tanggal, divisi, nominal, dan keterangan sebelum menyimpan. Lampiran bersifat opsional.
        </div>
    </div>
@endsection
