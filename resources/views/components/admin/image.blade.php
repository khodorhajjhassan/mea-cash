@props([
    'path' => null,
    'alt' => '',
])

@php
    $url = $path ? app(\App\Services\Media\ImageStorageService::class)->url($path) : null;
@endphp

@if($url)
    <img src="{{ $url }}" alt="{{ $alt }}" {{ $attributes }}>
@endif
