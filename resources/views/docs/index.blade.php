@extends('layouts.app')

@section('title', 'Panduan')

@section('content')
    <div class="mx-auto max-w-5xl py-4 sm:py-10">
        <header class="mb-7 max-w-2xl">
            <p class="text-sm font-bold uppercase text-emerald-700">Pusat bantuan</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Panduan Agrika Nota</h1>
            <p class="mt-3 leading-7 text-slate-600">Referensi singkat untuk mencatat, menyetujui, dan memeriksa nota.</p>
        </header>

        @include('docs.partials.navigation')

        <section class="mt-8 rounded-lg border border-emerald-200 bg-emerald-50 p-5">
            <h2 class="font-bold text-emerald-900">Alur singkat</h2>
            <p class="mt-2 text-sm leading-6 text-emerald-800">Admin membuat nota &rarr; IT menyetujui atau menolak &rarr;
                pemeriksa mengecek nota approved setiap minggu.</p>
        </section>
    </div>
@endsection
