@extends('layouts.app')
@section('title', 'Dashboard Pemeriksa')
@section('content')
<div class="mx-auto max-w-5xl py-3 sm:py-8">
    <div class="mb-6">
        <p class="text-sm font-semibold text-green-600">Pemeriksa</p>
        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Pemeriksaan Mingguan</h1>
        <p class="mt-1 text-sm text-slate-500">{{ now()->translatedFormat('F Y') }} · Pilih minggu untuk memeriksa nota dan foto.</p>
    </div>
    @include('weekly-reviews.partials.week-grid')
    <a href="{{ route('nota.index') }}" class="mt-5 block w-full bg-slate-900 px-4 py-3 text-center text-sm font-bold text-white sm:w-auto sm:inline-block">Lihat History Nota Approved</a>
</div>
@endsection
