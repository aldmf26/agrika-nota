@extends('layouts.app')
@section('title', 'Rekap Mingguan')
@section('content')
<div class="mx-auto max-w-5xl py-3 sm:py-8">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><h1 class="text-2xl font-bold text-slate-900">Rekap Mingguan</h1><p class="text-sm text-slate-500">Nota approved untuk pemeriksaan digital.</p></div>
        <form class="grid grid-cols-2 gap-2">
            <select name="month" class="border border-slate-200 px-3 py-2" onchange="this.form.submit()">@foreach(range(1,12) as $m)<option value="{{ $m }}" @selected($month===$m)>{{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}</option>@endforeach</select>
            <input name="year" type="number" value="{{ $year }}" class="border border-slate-200 px-3 py-2" onchange="this.form.submit()">
        </form>
    </div>
    @include('weekly-reviews.partials.week-grid')
</div>
@endsection
