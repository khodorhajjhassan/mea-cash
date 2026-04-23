@props([
    'alt' => 'MeaCash',
    'class' => '',
    'sizes' => '40px',
    'loading' => 'lazy',
    'fetchpriority' => 'auto',
])

@php
    $classes = trim('block flex-shrink-0 '.$class);
@endphp

<picture {{ $attributes->merge(['class' => $classes]) }}>
    <source
        type="image/webp"
        srcset="
            {{ asset('meacash-logo-64.webp') }} 64w,
            {{ asset('meacash-logo-128.webp') }} 128w,
            {{ asset('meacash-logo-256.webp') }} 256w,
            {{ asset('meacash-logo-512.webp') }} 512w
        "
        sizes="{{ $sizes }}">
    <source
        type="image/png"
        srcset="
            {{ asset('meacash-logo-128.png') }} 128w,
            {{ asset('meacash-logo-256.png') }} 256w
        "
        sizes="{{ $sizes }}">
    <img
        src="{{ asset('meacash-logo-128.png') }}"
        alt="{{ $alt }}"
        width="256"
        height="256"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchpriority }}"
        class="block h-full w-full object-contain">
</picture>
