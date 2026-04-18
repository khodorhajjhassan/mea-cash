@props(['variant' => 'primary', 'href' => null, 'icon' => null])

@php
    $baseClasses = "px-8 py-4 rounded-xl font-label font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:scale-105 active:scale-95";
    
    $variants = [
        'primary' => 'bg-gradient-to-r from-primary-fixed to-primary-container text-on-primary shadow-[0_0_30px_rgba(0,240,255,0.3)]',
        'secondary' => 'bg-gradient-to-r from-secondary-fixed to-secondary-container text-on-secondary shadow-[0_0_30px_rgba(254,0,254,0.3)]',
        'glass' => 'glass-panel text-on-surface hover:bg-surface-container-highest/60',
        'outline' => 'border border-outline-variant/30 text-on-surface hover:border-primary-container/50 hover:text-primary-container',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <span class="material-symbols-outlined text-sm">{{ $icon }}</span> @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        @if($icon) <span class="material-symbols-outlined text-sm">{{ $icon }}</span> @endif
        <span>{{ $slot }}</span>
    </button>
@endif
