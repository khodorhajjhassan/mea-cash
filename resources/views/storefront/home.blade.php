@extends('storefront.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'MeaCash - بطاقات رقمية وشحن ألعاب' : 'MeaCash - Digital Cards & Game Top-ups')

@section('content')
@php $locale = app()->getLocale(); @endphp
@php $activeSearchQuery = trim((string) ($searchQuery ?? request('q', ''))); @endphp

{{-- Hero Section --}}
<section class="py-6 lg:py-10">
    <div class="sf-panel sf-hero relative" style="padding: 1.5rem 1rem;">
        <div class="sf-hero-line"></div>
        <div class="sf-hero-glow"></div>

        <div class="relative z-10">
            <div class="sf-eyebrow">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ $locale == 'ar' ? 'تسليم رقمي فوري' : 'Instant Digital Delivery' }}
            </div>

            <h1 class="sf-hero-title font-heading">
                {{ $locale == 'ar' ? 'بطاقات رقمية واشتراكات بأفضل الأسعار' : 'Digital Cards & Subscriptions at Best Prices' }}
            </h1>

            <p class="mt-3 text-sm leading-6 sm:text-base sm:leading-7" style="color: var(--sf-muted);">
                {{ $locale == 'ar' ? 'اشترِ بطاقات الألعاب والاشتراكات الرقمية. ادفع عبر المحفظة واستلم فوراً.' : 'Buy game cards and digital subscriptions. Pay with your wallet and receive instantly.' }}
            </p>

            <div class="mt-4 flex flex-wrap gap-2 sm:mt-6 sm:gap-3">
                <span class="sf-trust-badge">{{ $locale == 'ar' ? '⚡ تسليم فوري' : '⚡ Instant Delivery' }}</span>
                <span class="sf-trust-badge">{{ $locale == 'ar' ? '🔒 دفع آمن' : '🔒 Secure Payment' }}</span>
                <span class="sf-trust-badge">{{ $locale == 'ar' ? '🌍 عربي & English' : '🌍 Arabic & English' }}</span>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="#products-section" class="sf-btn-gold w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    {{ $locale == 'ar' ? 'تسوق الآن' : 'Shop Now' }}
                </a>
                @guest
                    <a href="{{ route('store.register') }}" class="sf-btn-outline w-full sm:w-auto">
                        {{ $locale == 'ar' ? 'إنشاء حساب مجاني' : 'Create Free Account' }}
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

{{-- Category Bar --}}
@if($categories->isNotEmpty())
<div class="sf-category-bar" id="categories">
    <div class="sf-category-scroll" id="category-scroll">
        {{-- All Products --}}
        <a href="{{ route('store.home') }}" class="sf-cat-orb {{ !request('category') ? 'active' : '' }}">
            <span class="sf-cat-orb-icon">🎁</span>
            <span class="sf-cat-orb-label">{{ $locale == 'ar' ? 'الكل' : 'All' }}</span>
        </a>

        @foreach($categories as $cat)
        <a href="{{ route('store.home', ['category' => $cat->slug]) }}" class="sf-cat-orb {{ request('category') == $cat->slug ? 'active' : '' }}">
            <span class="sf-cat-orb-icon">{{ $cat->icon ?? '•' }}</span>
            <span class="sf-cat-orb-label">{{ $cat->{"name_{$locale}"} }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Products Grid --}}
<section id="products-section" class="py-4">
    <div class="mb-4 flex items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold sm:text-2xl font-heading" style="color: var(--sf-text);">
                {{ $activeSearchQuery !== '' ? ($locale == 'ar' ? 'نتائج البحث' : 'Search Results') : ($locale == 'ar' ? 'المنتجات' : 'Products') }}
            </h2>
            <p class="mt-1 text-sm leading-6" style="color: var(--sf-muted);">
                {{ $activeSearchQuery !== '' ? ($locale == 'ar' ? 'النتائج للكلمة:' : 'Results for:') . ' "' . $activeSearchQuery . '"' : ($locale == 'ar' ? 'اختر المنتج ثم أضفه للسلة.' : 'Choose a product and add it to your cart.') }}
            </p>
        </div>
        @if($activeSearchQuery !== '')
            <a href="{{ route('store.home') }}" class="sf-btn-outline" style="height: 2rem; font-size: 0.75rem; padding: 0 0.75rem;">
                {{ $locale == 'ar' ? 'مسح البحث' : 'Clear Search' }}
            </a>
        @endif
    </div>

    @if(method_exists($products, 'count') && $products->count() > 0 || (is_countable($products) && count($products) > 0))
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-5 xl:gap-4">
        @foreach($products as $product)
        @php
            $name = $product->{"name_{$locale}"};
            $catName = $product->subcategory?->category?->{"name_{$locale}"} ?? '';
            $price = $product->packages->isNotEmpty() ? $product->packages->first()->selling_price : $product->selling_price;
        @endphp
        <div data-product-slug="{{ $product->slug }}" class="sf-product-card group cursor-pointer">
            <div class="sf-product-card-img">
                @if($product->is_featured)
                    <span class="sf-hot-badge">{{ $locale == 'ar' ? '🔥 مميز' : '🔥 Featured' }}</span>
                @endif
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $name }}" loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center text-4xl opacity-40">🎮</div>
                @endif
            </div>
            <div class="sf-product-card-body">
                <h3 class="sf-product-card-name">{{ $name }}</h3>
                <p class="sf-product-card-cat">{{ $catName }}</p>
                <div class="sf-product-card-price">
                    <span class="sf-price-current">${{ number_format($price, 2) }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if(method_exists($products, 'links'))
    <div class="mt-8 flex justify-center">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif

    @else
    <div class="sf-panel p-6 text-center" style="border-radius: var(--sf-radius-lg);">
        <p class="text-base font-semibold" style="color: var(--sf-text);">
            {{ $activeSearchQuery !== '' ? ($locale == 'ar' ? 'لا توجد نتائج مطابقة' : 'No results found') : ($locale == 'ar' ? 'لا توجد منتجات حالياً' : 'No products available') }}
        </p>
        <p class="mt-2 text-sm" style="color: var(--sf-muted);">
            {{ $activeSearchQuery !== '' ? ($locale == 'ar' ? 'جرّب كلمة مختلفة أو تحقق من الإملاء.' : 'Try a different keyword or check spelling.') : ($locale == 'ar' ? 'جرّب فئة أخرى أو عد لاحقاً.' : 'Try another category or come back later.') }}
        </p>
    </div>
    @endif
</section>

{{-- How It Works --}}
<section id="how-it-works" class="py-6">
    <div class="sf-panel p-5 sm:p-8" style="border-radius: var(--sf-radius-xl);">
        <h2 class="text-xl font-bold sm:text-2xl font-heading" style="color: var(--sf-text);">
            {{ $locale == 'ar' ? 'كيف يعمل الموقع' : 'How It Works' }}
        </h2>
        @php
        $steps = $locale == 'ar'
            ? ['اختر المنتج والباقة المناسبة.', 'أضفه للسلة وأكمل الطلب.', 'ادفع من رصيد محفظتك.', 'استلم الكود أو بيانات الحساب فوراً.']
            : ['Choose a product and package.', 'Add to cart and checkout.', 'Pay with your wallet balance.', 'Receive your code or account instantly.'];
        @endphp
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 xl:grid-cols-4">
            @foreach($steps as $idx => $step)
            <div class="rounded-[18px] p-4 sm:rounded-[22px] sm:p-5" style="border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05);">
                <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold" style="background: rgba(216,154,29,0.15); color: var(--sf-gold-light);">
                    {{ $idx + 1 }}
                </div>
                <p class="mt-3 text-sm leading-6" style="color: var(--sf-muted);">{{ $step }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
