@extends('storefront.layouts.app')
@section('title', $category->{"name_" . app()->getLocale()} . ' - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs mb-6" style="color: var(--sf-muted);">
        <a href="{{ route('store.home') }}" class="hover:underline" style="color: var(--sf-gold-light);">{{ $locale == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span>/</span>
        <span>{{ $category->{"name_{$locale}"} }}</span>
    </div>

    {{-- Category Header --}}
    <div class="sf-panel p-5 sm:p-8 mb-6" style="border-radius: var(--sf-radius-xl);">
        <div class="flex items-center gap-4">
            @if($category->icon)
                <div class="text-3xl">{{ $category->icon }}</div>
            @endif
            <div>
                <h1 class="text-2xl font-bold sm:text-3xl font-heading" style="color: var(--sf-text);">{{ $category->{"name_{$locale}"} }}</h1>
                <p class="mt-1 text-sm" style="color: var(--sf-muted);">{{ $products->total() }} {{ $locale == 'ar' ? 'منتج' : 'products' }}</p>
            </div>
        </div>
    </div>

    {{-- Subcategory Chips --}}
    @if($subcategories->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($subcategories as $sub)
        <span class="sf-trust-badge">{{ $sub->{"name_{$locale}"} }}</span>
        @endforeach
    </div>
    @endif

    {{-- Products Grid --}}
    @if($products->isNotEmpty())
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-5 xl:gap-4">
        @foreach($products as $product)
        @php
            $name = $product->{"name_{$locale}"};
            $price = $product->packages->isNotEmpty() ? $product->packages->first()->selling_price : $product->selling_price;
        @endphp
        <div data-product-slug="{{ $product->slug }}" class="sf-product-card group cursor-pointer">
            <div class="sf-product-card-img">
                @if($product->is_featured)
                    <span class="sf-hot-badge">🔥</span>
                @endif
                @if($product->image)
                    <img src="{{ str_starts_with($product->image, 'http') ? $product->image : Storage::url($product->image) }}" alt="{{ $name }}" loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center text-4xl opacity-40">🎮</div>
                @endif
            </div>
            <div class="sf-product-card-body">
                <h3 class="sf-product-card-name">{{ $name }}</h3>
                <div class="sf-product-card-price">
                    <span class="sf-price-current">${{ number_format($price, 2) }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
    @else
    <div class="sf-panel p-6 text-center" style="border-radius: var(--sf-radius-lg);">
        <p class="text-base font-semibold" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'لا توجد منتجات في هذه الفئة' : 'No products in this category' }}</p>
    </div>
    @endif
</div>
@endsection
