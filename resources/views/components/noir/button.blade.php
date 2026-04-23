@props(['variant' => 'primary', 'href' => null, 'icon' => null])

@php
    $baseClasses = "px-8 py-4 rounded-xl font-label font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:scale-105 active:scale-95";
    
    $variants = [
        'primary' => 'bg-gradient-to-r from-primary-container to-secondary-container text-on-primary-container shadow-[0_0_30px_rgba(0,240,255,0.3)]',
        'secondary' => 'bg-gradient-to-r from-secondary-fixed to-secondary-container text-on-secondary shadow-[0_0_30px_rgba(254,0,254,0.3)]',
        'glass' => 'glass-panel text-on-surface hover:bg-surface-container-highest/60',
        'outline' => 'border border-outline-variant/30 text-on-surface hover:border-primary-container/50 hover:text-primary-container',
        'gradient' => 'bg-gradient-to-r from-primary-container to-secondary-container text-on-surface shadow-lg border border-outline-variant/30 hover:shadow-primary-container/20 group/btn',
        'gradient-outline' => 'sf-gradient-border text-white hover:text-white/80 hover:opacity-90 transition-all',
        'white-outline' => 'border border-white/20 text-white/90 hover:border-white/60 hover:bg-white/5',
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
