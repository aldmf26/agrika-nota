@props(['message', 'type' => 'success'])

@php
    $classes = match ($type) {
        'warning' => 'bg-amber-500 border-amber-600',
        'error' => 'bg-red-600 border-red-700',
        default => 'bg-green-600 border-green-700',
    };
@endphp

<div id="globalToast" role="status" aria-live="polite"
    class="fixed inset-x-3 top-20 z-50 mx-auto flex max-w-lg items-start gap-3 border px-4 py-3 text-white shadow-lg sm:inset-x-auto sm:right-4 sm:mx-0 {{ $classes }}">
    <p class="min-w-0 flex-1 break-words text-sm font-medium leading-5">{{ $message }}</p>
    <button type="button" id="closeGlobalToast" data-toast-close
        class="flex h-6 w-6 shrink-0 items-center justify-center text-xl leading-none text-white/80 hover:text-white"
        aria-label="Tutup notifikasi">&times;</button>
</div>

@once
    <script>
        (() => {
            const toast = document.getElementById('globalToast');
            const closeToast = () => toast?.remove();
            toast?.querySelector('[data-toast-close]')?.addEventListener('click', closeToast);
            window.setTimeout(closeToast, 5000);
        })();
    </script>
@endonce
