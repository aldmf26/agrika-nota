<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nota System') - Agrika Nota</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Flash Messages -->
    @php
        $flash = collect([
            'success' => ['message' => session('success'), 'classes' => 'bg-green-600 border-green-700'],
            'warning' => ['message' => session('warning'), 'classes' => 'bg-amber-500 border-amber-600'],
            'error' => ['message' => session('error'), 'classes' => 'bg-red-600 border-red-700'],
        ])->first(fn ($item) => filled($item['message']));
    @endphp
    @if ($flash)
        <div id="globalToast" role="status" aria-live="polite"
            class="fixed inset-x-3 top-20 z-50 mx-auto flex max-w-lg items-start gap-3 border px-4 py-3 text-white shadow-lg sm:inset-x-auto sm:right-4 sm:mx-0 {{ $flash['classes'] }}">
            <p class="min-w-0 flex-1 break-words text-sm font-medium leading-5">{{ $flash['message'] }}</p>
            <button type="button" id="closeGlobalToast"
                class="flex h-6 w-6 shrink-0 items-center justify-center text-xl leading-none text-white/80 hover:text-white"
                aria-label="Tutup notifikasi">&times;</button>
        </div>
        <script>
            (() => {
                const toast = document.getElementById('globalToast');
                const closeToast = () => toast?.remove();
                document.getElementById('closeGlobalToast')?.addEventListener('click', closeToast);
                window.setTimeout(closeToast, 5000);
            })();
        </script>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')
</body>

</html>
