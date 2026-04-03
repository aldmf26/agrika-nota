@props(['status' => 'draft', 'class' => ''])

@php
    $statusClass = match ($status) {
        'draft' => 'background: #f1f5f9; color: #475569; box-shadow: inset 0 0 0 1px rgba(71, 85, 105, 0.1);',
        'pending' => 'background: #fef3c7; color: #b45309; box-shadow: inset 0 0 0 1px rgba(180, 83, 9, 0.1);',
        'approved' => 'background: #dcfce7; color: #15803d; box-shadow: inset 0 0 0 1px rgba(21, 128, 61, 0.1);',
        'rejected' => 'background: #fee2e2; color: #b91c1c; box-shadow: inset 0 0 0 1px rgba(185, 28, 28, 0.1);',
        'void' => 'background: #f3f4f6; color: #374151; box-shadow: inset 0 0 0 1px rgba(55, 65, 81, 0.1);',
        default => 'background: #f1f5f9; color: #475569; box-shadow: inset 0 0 0 1px rgba(71, 85, 105, 0.1);',
    };

    $statusLabel = match ($status) {
        'draft' => '📝 Draft',
        'pending' => '⏳ Pending',
        'approved' => '✓ Approved',
        'rejected' => '✗ Rejected',
        'void' => '⊘ Void',
        default => ucfirst($status),
    };
@endphp

<span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $class }}" style="{{ $statusClass }}">
    {{ $statusLabel }}
</span>
