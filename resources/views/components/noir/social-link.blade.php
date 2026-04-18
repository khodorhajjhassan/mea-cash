@props(['icon', 'color' => 'primary', 'href' => '#'])

@php
    $colorClasses = [
        'primary' => 'text-on-surface-variant hover:text-primary-container hover:border-primary-container',
        'secondary' => 'text-on-surface-variant hover:text-secondary-container hover:border-secondary-container',
    ];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'w-10 h-10 rounded-full glass-panel flex items-center justify-center transition-all ' . ($colorClasses[$color] ?? $colorClasses['primary'])]) }}>
    <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
</a>
