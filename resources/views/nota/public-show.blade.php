<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Nota {{ $nota->nomor_nota }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-lg">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full {{ $verification === 'approval' && $nota->status !== 'approved' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
            <span class="text-3xl font-bold">{{ $verification === 'approval' && $nota->status !== 'approved' ? '!' : '✓' }}</span>
        </div>

        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-slate-500">{{ $nota->nomor_nota }}</p>

        @if($verification === 'creator')
            <h1 class="text-2xl font-bold text-slate-900">Data Pembuat Nota</h1>
            <p class="mt-4 text-lg text-slate-700">Nota dibuat oleh:</p>
            <p class="mt-1 text-xl font-bold text-slate-900">{{ $nota->user->name }}</p>
        @elseif($nota->status === 'approved')
            <h1 class="text-2xl font-bold text-emerald-700">Nota Sudah Terverifikasi</h1>
        @else
            <h1 class="text-2xl font-bold text-amber-700">Nota Belum Terverifikasi</h1>
        @endif
    </main>
</body>
</html>
