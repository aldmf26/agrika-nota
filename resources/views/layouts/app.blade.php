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
        $flashes = [
            'error' => session('error') ?: $errors->first(),
            'warning' => session('warning'),
            'success' => session('success'),
        ];
        $flashType = collect($flashes)->search(fn ($message) => filled($message));
        $flashMessage = $flashType !== false ? $flashes[$flashType] : null;
    @endphp
    @if ($flashMessage)
        <x-toast :type="$flashType" :message="$flashMessage" />
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
