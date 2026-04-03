@props(['title' => '', 'subtitle' => '', 'class' => ''])

<div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow {{ $class }}">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        @if ($subtitle)
            <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>
