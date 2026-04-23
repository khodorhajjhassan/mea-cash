@props([
    'alt' => 'MeaCash',
    'class' => '',
    'sizes' => '40px',
    'loading' => 'lazy',
    'fetchpriority' => 'auto',
])

@php
    $classes = trim('block flex-shrink-0 '.$class);
    $requestedSize = (int) preg_replace('/[^0-9]/', '', (string) $sizes);
    $requestedSize = $requestedSize > 0 ? $requestedSize : 40;

    $webpSizes = match (true) {
        $requestedSize <= 48 => [64, 128, 256],
        $requestedSize <= 128 => [128, 256, 512],
        default => [256, 512],
    };

    $pngSizes = match (true) {
        $requestedSize <= 48 => [64, 128],
        $requestedSize <= 128 => [128, 256],
        default => [256],
    };

    $webpSrcset = collect($webpSizes)
        ->map(fn (int $size) => asset("meacash-logo-{$size}.webp")." {$size}w")
        ->implode(', ');

    $pngSrcset = collect($pngSizes)
        ->map(fn (int $size) => asset("meacash-logo-{$size}.png")." {$size}w")
        ->implode(', ');
@endphp

<picture {{ $attributes->merge(['class' => $classes]) }}>
    <source
        type="image/webp"
        srcset="{{ $webpSrcset }}"
        sizes="{{ $sizes }}">
    <source
        type="image/png"
        srcset="{{ $pngSrcset }}"
        sizes="{{ $sizes }}">
    <img
        src="{{ asset('meacash-logo-' . $webpSizes[0] . '.webp') }}"
        alt="{{ $alt }}"
        width="{{ $requestedSize }}"
        height="{{ $requestedSize }}"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchpriority }}"
        class="block h-full w-full object-contain"
        onerror="this.onerror=null; this.src='{{ asset('meacash-logo-128.png') }}';">
</picture>
