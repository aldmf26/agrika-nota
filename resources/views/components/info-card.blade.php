@props(['label' => '', 'value' => '', 'icon' => ''])

<div class="bg-white rounded-lg border border-gray-200 p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-600">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
        </div>
        @if ($icon)
            <div class="text-3xl">{{ $icon }}</div>
        @endif
    </div>
</div>
