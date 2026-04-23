@extends('storefront.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'MeaCash - بطاقات رقمية وشحن ألعاب' : 'MeaCash - Digital Cards & Game Top-ups')

@section('content')
    @php $locale = app()->getLocale(); @endphp
    @php $activeSearchQuery = trim((string) ($searchQuery ?? request('q', ''))); @endphp
    @php
        $homepageSections = $homepageSections ?? collect();
        $howItWorksSections = $howItWorksSections ?? collect();
        $contentHomepageSections = $homepageSections->filter(fn($payload) => $payload['section']->isContentBlock());
        $cryptoHomepageSections = $contentHomepageSections->filter(fn($payload) => $payload['section']->type === \App\Models\HomepageSection::TYPE_CRYPTO_CARD);
        $productHomepageSections = $homepageSections->reject(fn($payload) => $payload['section']->isContentBlock());
        
        $middleBanners = $banners->get('middle') ?? collect();
        $leftBanners = $banners->get('left') ?? collect();
        $rightBanners = $banners->get('right') ?? collect();

        $firstBanner = $middleBanners->first();
        $firstBannerDesktopUrl = $firstBanner?->imageUrl($locale);
        $firstBannerMobileUrl = $firstBanner?->mobileImageUrl($locale);
        $firstBannerPreloadUrl = $firstBannerMobileUrl ?: $firstBannerDesktopUrl;
        $firstBannerPreloadSrcset = $firstBannerMobileUrl && $firstBannerDesktopUrl && $firstBannerMobileUrl !== $firstBannerDesktopUrl
            ? $firstBannerMobileUrl . ' 768w, ' . $firstBannerDesktopUrl . ' 1440w'
            : null;
            
        $leftStaticBanner = $leftBanners->first();
        $rightStaticBanner = $rightBanners->first();
    @endphp

    @if($firstBannerDesktopUrl)
        @push('styles')
            <link rel="preload" as="image" href="{{ $firstBannerPreloadUrl }}" @if($firstBannerPreloadSrcset)
            imagesrcset="{{ $firstBannerPreloadSrcset }}" imagesizes="100vw" @endif fetchpriority="high">
        @endpush
    @endif

    {{-- Hero Banner Carousel Section --}}
    <section class="relative px-4 md:px-8 pt-6 pb-6 z-10 sf-reveal-section">
        <div class="mc-hero-shell mx-auto grid w-full max-w-[1440px] lg:grid-cols-[0.9fr_1.65fr_0.9fr]">
            @if($leftStaticBanner)
                <a href="{{ $leftStaticBanner->link ?: '#products-section' }}"
                    class="mc-hero-static-card hidden h-[260px] w-full overflow-hidden rounded-l-md sm:h-[320px] lg:h-[420px] xl:h-[460px] lg:block">
                    <img src="{{ $leftStaticBanner->imageUrl($locale) }}" alt="{{ $leftStaticBanner->{"title_$locale"} }}"
                        class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.02]" loading="lazy"
                        decoding="async">
                </a>
            @endif

            <div id="hero-carousel"
                class="group relative h-[300px] w-full overflow-hidden rounded-md shadow-2xl md:rounded-none sm:h-[320px] lg:h-[420px] xl:h-[460px]">
                <div class="carousel-inner h-full w-full flex transition-transform duration-700 ease-in-out" dir="ltr">
                    @forelse($middleBanners as $banner)
                        <div class="carousel-item min-w-full h-full relative sf-skeleton">
                            <img class="w-full h-full object-cover sf-img-loading" src="{{ $banner->mobileImageUrl($locale) }}"
                                srcset="{{ $banner->mobileImageUrl($locale) }} 768w, {{ $banner->imageUrl($locale) }} 1440w" sizes="100vw"
                                alt="{{ $banner->{"title_$locale"} }}" width="1440" height="720"
                                fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="{{ $loop->first ? 'sync' : 'async' }}"
                                onload="this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');">
                            <div
                                class="mc-carousel-overlay absolute inset-0 bg-gradient-to-t md:bg-gradient-to-s from-background via-background/20 to-transparent">
                            </div>

                            <div class="absolute inset-0 flex ro items-center p-6 sm:p-10 md:p-16 lg:p-10 xl:p-14">
                                <div class="max-w-2xl">
                                    <h1
                                        class="font-headline px-2 text-2xl font-black italic leading-[1.1] tracking-tighter sm:text-5xl lg:text-5xl xl:text-6xl mb-3 sm:mb-4 animate-fade-in-up sf-text-gradient">
                                        {{ $banner->{"title_$locale"} }}
                                    </h1>
                                    <p
                                        class="text-on-surface-variant text-sm md:text-xl lg:text-base xl:text-lg max-w-lg leading-relaxed animate-fade-in-up-delay">
                                        {{ $banner->{"description_$locale"} }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="carousel-item min-w-full h-full relative">
                            <img class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDrlznlKgHHa63HOhVKSNWE8C5o6YWGzqxbSrsVKR6imUOq2BDzZoRlqJg_aBtStZO89zUqnzPz4cUR1Ar_9KPYAsyplUSUhl7Cu69sWYscBbkmZv8_Z23wFHRJUsaHoWrCgTg_AZAPtY_FpHMiau3uk0SCMp2vwzAl9Sk5ydgPkW2up5bhPyu8FmcOIpMoaTLYNwC-ofII6e2sndmu9_tc47MTiFoRRkToqSy-lC4CowcwR89nZBqQxnz4mrEdSPnNMxpTJO40tQ"
                                alt="Default Hero Image" width="1440" height="720" fetchpriority="high" loading="eager"
                                decoding="sync">
                            <div
                                class="mc-carousel-overlay absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent">
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Carousel Hub: Indicators & Global CTA --}}
                @if($middleBanners->count() > 0)
                    <div
                        class="absolute bottom-4 sm:bottom-10 right-4 sm:right-10 flex flex-col-reverse sm:flex-row items-center gap-3 sm:gap-8 z-30">
                        <div class="flex gap-2.5">
                            @foreach($middleBanners as $index => $banner)
                                <button
                                    class="carousel-indicator w-2 h-2 rounded-full border border-white/20 transition-all hover:scale-125 {{ $index === 0 ? 'bg-primary-container border-primary-container w-3' : '' }}"
                                    data-index="{{ $index }}" aria-label="{{ __('Show banner') }} {{ $index + 1 }}"></button>
                            @endforeach
                        </div>

                        <div id="banner-cta-container">
                            @foreach($middleBanners as $index => $banner)
                                @php
                                    $buttonText = trim((string) ($banner->{"button_text_$locale"} ?: $banner->button_text_en ?: ''));
                                @endphp
                                <div class="banner-cta-item {{ $index === 0 ? '' : 'hidden' }}" data-index="{{ $index }}">
                                    @if($buttonText !== '')
                                        <x-noir.button variant="primary" href="{{ $banner->link }}" icon="bolt"
                                            class="!px-3.5 !py-2 sm:!px-7 sm:!py-3.5 text-[10px] sm:text-xs min-w-[90px] sm:min-w-[120px]">
                                            {{ $buttonText }}
                                        </x-noir.button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button
                        class="absolute top-1/2 left-6 -translate-y-1/2 w-12 h-12 rounded-full glass-panel hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white/10"
                        onclick="prevSlide()" aria-label="{{ __('Previous banner') }}">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button
                        class="absolute top-1/2 right-6 -translate-y-1/2 w-12 h-12 rounded-full glass-panel hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white/10"
                        onclick="nextSlide()" aria-label="{{ __('Next banner') }}">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                @endif
            </div>

            @if($rightStaticBanner)
                <a href="{{ $rightStaticBanner->link ?: '#products-section' }}"
                    class="mc-hero-static-card hidden h-[260px] w-full overflow-hidden rounded-r-md sm:h-[320px] lg:h-[420px] xl:h-[460px] lg:block">
                    <img src="{{ $rightStaticBanner->imageUrl($locale) }}" alt="{{ $rightStaticBanner->{"title_$locale"} }}"
                        class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.02]" loading="lazy"
                        decoding="async">
                </a>
            @endif
        </div>
    </section>

    {{-- High-Fidelity Infinite Brand Image Strip --}}
    @php
        $brandTiles = $featuredSubcategories->count() > 0
            ? ($featuredSubcategories->count() < 10 ? $featuredSubcategories->merge($featuredSubcategories) : $featuredSubcategories)
            : collect();
    @endphp
    <div class="overflow-hidden py-3 sf-lazy-section">
        <div class="{{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }} flex w-max items-center gap-3 md:gap-4 py-2"
            dir="ltr">
            @foreach($brandTiles as $sub)
                @php
                    $subImage = $sub->image ? (str_starts_with($sub->image, 'http') ? $sub->image : \Illuminate\Support\Facades\Storage::url($sub->image)) : asset('meacash-logo-64.webp');
                    $subName = $sub->{"name_$locale"} ?: $sub->name_en;
                @endphp
                <div onclick="window.openSubcategoryModal('{{ $sub->slug }}')"
                    class="sf-brand-card group flex h-28 w-28 md:h-32 md:w-32 shrink-0 flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface-container/55 p-4 transition-all duration-300 hover:border-primary-container/70 hover:bg-surface-container-high hover:shadow-[0_0_30px_rgba(0,240,255,0.12)] cursor-pointer">
                    <div class="sf-brand-card-icon-shell mb-3 flex h-12 w-12 items-center justify-center rounded-2xl">
                        <img src="{{ $subImage }}" alt="{{ $subName }}" loading="lazy" width="36" height="36" decoding="async"
                            class="sf-brand-card-icon h-9 w-9 object-contain opacity-90 transition-all duration-300 group-hover:scale-110 group-hover:opacity-100"
                            onerror="this.onerror=null; this.src='{{ asset('meacash-logo-128.png') }}';">
                    </div>
                    <span
                        class="sf-brand-card-label w-full truncate text-center font-headline text-[9px] font-black uppercase tracking-widest text-on-surface/45 transition-colors group-hover:text-primary-container">
                        {{ $subName }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    {{-- Unified Infinite Brand Marquee --}}
    <section
        class="sf-brand-marquee w-full py-8 border-y border-outline-variant/10 bg-surface-container-lowest/50 backdrop-blur-sm overflow-hidden z-10 relative sf-reveal-section sf-lazy-section">
        <div class="flex items-center gap-16 md:gap-32 w-max {{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }}"
            dir="ltr">
            @php
                $marqueeItems = $featuredSubcategories->count() > 0
                    ? ($featuredSubcategories->count() < 10 ? $featuredSubcategories->merge($featuredSubcategories)->merge($featuredSubcategories) : $featuredSubcategories)
                    : collect();
            @endphp
            @foreach($marqueeItems as $sub)
                @php
                    $subImage = $sub->image ? (str_starts_with($sub->image, 'http') ? $sub->image : \Illuminate\Support\Facades\Storage::url($sub->image)) : asset('meacash-logo-64.webp');
                    $subName = $sub->{"name_$locale"} ?: $sub->name_en;
                @endphp
                <div onclick="window.openSubcategoryModal('{{ $sub->slug }}')"
                    class="sf-brand-marquee-item flex items-center gap-3 group/brand cursor-pointer opacity-70 hover:opacity-100 transition-all duration-500 shrink-0">
                    <img src="{{ $subImage }}" alt="{{ $subName }}" loading="lazy" width="28" height="28" decoding="async"
                        class="sf-brand-marquee-icon h-7 w-7 object-contain transition-transform group-hover/brand:scale-110"
                        onerror="this.onerror=null; this.src='{{ asset('meacash-logo-128.png') }}';">
                    <span
                        class="sf-brand-marquee-label font-headline font-black text-xl tracking-widest uppercase italic text-on-surface/50 group-hover/brand:text-primary-container">
                        {{ $subName }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- High-Fidelity Circular Category Bar --}}
    @if($categories->isNotEmpty())
        <section
            class="sf-category-strip sticky top-20 z-40 hidden px-4 py-8 bg-background/90 backdrop-blur-md border-b border-outline-variant/5 sf-reveal-section sf-lazy-section md:block md:px-8">
            <div class="flex justify-center">
                <div
                    class="flex items-center gap-6 md:gap-10 overflow-x-auto no-scrollbar pb-2 w-full max-w-[1440px] justify-start">
                    @php
                        $allAssetsIcon = 'apps';
                        $hotActive = request()->boolean('featured');
                        $allActive = !request('category') && !request('featured');
                    @endphp
                    <a href="{{ route('store.home', ['featured' => 1]) }}#products-section" data-category-link="hot"
                        data-category-color="#fe00fe" class="sf-category-item flex flex-col items-center gap-4 shrink-0 group">
                        <div data-category-circle
                            class="w-16 h-16 md:w-20 md:h-20 flex items-center justify-center transition-all duration-300 bg-surface-container-highest text-on-surface-variant border {{ $hotActive ? 'border-[#fe00fe] ring-2 ring-[#fe00fe]/40 shadow-[0_0_22px_rgba(254,0,254,0.24)]' : 'border-outline-variant/20 group-hover:border-[#fe00fe]/70 group-hover:text-[#fe00fe]' }} shadow-lg"
                            style="border-radius: 9999px !important;">
                            <span
                                class="material-symbols-outlined text-3xl md:text-4xl {{ $hotActive ? 'text-[#fe00fe]' : 'text-on-surface-variant group-hover:text-[#fe00fe]' }}">local_fire_department</span>
                        </div>
                        <span data-category-label
                            class="text-[10px] md:text-xs font-headline font-black uppercase tracking-[0.2em] text-center {{ $hotActive ? 'text-[#fe00fe] drop-shadow-md' : 'text-on-surface-variant/70 group-hover:text-[#fe00fe]' }}"
                            style="min-height: 28px;">
                            {{ __('Hot Deals') }}
                        </span>
                    </a>

                    <a href="{{ route('store.home') }}#products-section" data-category-link="all" data-category-color="#fbbf24"
                        class="sf-category-item flex flex-col items-center gap-4 shrink-0 group">
                        <div data-category-circle
                            class="w-16 h-16 md:w-20 md:h-20 flex items-center justify-center transition-all duration-300 bg-surface-container-highest text-on-surface-variant border {{ $allActive ? 'border-[#fbbf24] ring-2 ring-[#fbbf24]/40 shadow-[0_0_22px_rgba(251,191,36,0.22)]' : 'border-outline-variant/20 group-hover:border-[#fbbf24]/70 group-hover:text-[#fbbf24]' }} shadow-lg"
                            style="border-radius: 9999px !important;">
                            <span
                                class="material-symbols-outlined text-3xl md:text-4xl {{ $allActive ? 'text-[#fbbf24]' : 'text-on-surface-variant group-hover:text-[#fbbf24]' }}">{{ $allAssetsIcon }}</span>
                        </div>
                        <span data-category-label
                            class="text-[10px] md:text-xs font-headline font-black uppercase tracking-[0.2em] text-center {{ $allActive ? 'text-[#fbbf24] drop-shadow-md' : 'text-on-surface-variant/70 group-hover:text-[#fbbf24]' }}"
                            style="min-height: 28px;">
                            {{ __('noir.all_assets') }}
                        </span>
                    </a>

                    @foreach($categories as $cat)
                        @php
                            $icon = $cat->icon ?: 'category';
                            if (mb_strlen($icon) <= 2)
                                $icon = 'category';

                            if (str_contains(strtolower($cat->name_en), 'game'))
                                $icon = 'sports_esports';
                            if (str_contains(strtolower($cat->name_en), 'gift'))
                                $icon = 'card_giftcard';
                            if (str_contains(strtolower($cat->name_en), 'streaming'))
                                $icon = 'movie';
                            if (str_contains(strtolower($cat->name_en), 'hot'))
                                $icon = 'local_fire_department';
                            if (str_contains(strtolower($cat->name_en), 'playstation'))
                                $icon = 'sports_esports';
                            if (str_contains(strtolower($cat->name_en), 'software'))
                                $icon = 'desktop_windows';
                            if (str_contains(strtolower($cat->name_en), 'social'))
                                $icon = 'forum';
                            if (str_contains(strtolower($cat->name_en), 'console'))
                                $icon = 'videogame_asset';
                        @endphp
                        <a href="{{ route('store.home', ['category' => $cat->slug]) }}#products-section"
                            data-category-link="{{ $cat->slug }}" data-category-color="#00f0ff"
                            class="sf-category-item flex flex-col items-center gap-4 shrink-0 group">
                            <div data-category-circle
                                class="w-16 h-16 md:w-20 md:h-20 flex items-center justify-center transition-all duration-300 bg-surface-container-highest text-on-surface-variant border {{ request('category') == $cat->slug ? 'border-primary-container ring-2 ring-primary-container/40 shadow-[0_0_22px_rgba(0,240,255,0.22)]' : 'border-outline-variant/20 group-hover:border-primary-container/70 group-hover:text-primary-container' }} shadow-lg"
                                style="border-radius: 9999px !important;">
                                @if(mb_strlen($cat->icon) <= 2 && $cat->icon)
                                    <span class="text-3xl md:text-4xl">{{ $cat->icon }}</span>
                                @else
                                    <span
                                        class="material-symbols-outlined text-3xl md:text-4xl {{ request('category') == $cat->slug ? 'text-primary-container' : 'text-on-surface-variant group-hover:text-primary-container' }}">{{ $icon }}</span>
                                @endif
                            </div>
                            <span data-category-label
                                class="text-[10px] md:text-xs font-headline font-black uppercase tracking-[0.2em] text-center {{ request('category') == $cat->slug ? 'text-primary-container drop-shadow-md' : 'text-on-surface-variant/70 group-hover:text-on-surface' }}"
                                style="min-height: 28px;">
                                {{ $cat->{"name_$locale"} }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Main Product Grid --}}
    <section id="products-section" class="px-4 md:px-8 py-12 min-h-[400px] sf-reveal-section sf-lazy-section">
        <x-noir.section-heading :title="$activeSearchQuery !== '' ? __('Search Results') : __('Discover Our Products')"
            :subtitle="$activeSearchQuery !== '' ? __('Search') : __('Premium Assets')" :gradient="true" />

        <div id="product-grid"
            class="grid grid-cols-3 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 md:gap-6">
            @include('storefront.partials.product-grid-items', ['products' => $products])
        </div>

        {{-- Load More --}}
        @if($products->hasMorePages())
            <div class="mt-12 flex justify-center">
                <x-noir.button variant="gradient-outline" id="load-more-btn" data-url="{{ $products->nextPageUrl() }}"
                    icon="expand_more" class="text-[10px] md:text-xs">
                    {{ __('Load More') }}
                </x-noir.button>
            </div>
        @endif

    </section>

    @if($cryptoHomepageSections->isNotEmpty())
        @foreach($cryptoHomepageSections as $homepageSection)
            @include('storefront.partials.homepage-section', $homepageSection)
        @endforeach
    @endif

    @if($productHomepageSections->isNotEmpty())
        @foreach($productHomepageSections as $homepageSection)
            @include('storefront.partials.homepage-section', $homepageSection)
        @endforeach
    @endif

    {{-- Community Feedback Marquee --}}
    @if(false && ($featuredFeedbacks ?? collect())->isNotEmpty())
        <section
            class="py-16 md:py-24 border-y border-outline-variant/10 bg-surface-container-low/30 relative overflow-hidden sf-reveal-section sf-lazy-section">
            <div class="sf-home-atmosphere opacity-20" aria-hidden="true">
                <div class="sf-home-orb sf-home-orb-magenta" style="left: 80%; top: 20%;"></div>
            </div>

            <div class="px-4 md:px-8 mb-12">
                <x-noir.section-heading :title="__('What Our Community Says')" :subtitle="__('User Reviews')" :centered="true"
                    :gradient="true" />
            </div>

            <div class="overflow-hidden">
                <div class="{{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }} flex w-max items-stretch gap-6 px-10"
                    dir="ltr">
                    @php
                        $feedbackCards = $featuredFeedbacks->count() < 6 ? $featuredFeedbacks->concat($featuredFeedbacks) : $featuredFeedbacks;
                    @endphp
                    @foreach($feedbackCards as $fb)
                        <div
                            class="sf-feedback-card group w-72 md:w-96 p-6 rounded-3xl border border-outline-variant/15 bg-surface-container/60 backdrop-blur-md flex flex-col justify-between transition-all duration-500 hover:border-primary-container/40 hover:bg-surface-container-high hover:shadow-[0_0_40px_rgba(0,240,255,0.08)]">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-container/20 to-secondary-container/20 border border-outline-variant/30 flex items-center justify-center font-headline font-black text-primary-container">
                                            {{ substr($fb->user?->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-headline text-sm font-black text-on-surface">
                                                {{ $fb->user?->name ?? 'Anonymous' }}
                                            </div>
                                            <div class="text-[9px] uppercase tracking-widest text-outline">
                                                {{ __('Verified Buyer') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-0.5 text-amber-500">
                                        @for($i = 0; $i < 5; $i++)
                                            <span class="material-symbols-outlined text-[16px]"
                                                style="font-variation-settings: 'FILL' {{ $i < ($fb->rating ?? 5) ? 1 : 0 }}">star</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm italic leading-relaxed text-on-surface-variant line-clamp-4">
                                    "{{ $fb->comment ?: ($locale === 'ar' ? 'خدمة رائعة وسريعة جداً!' : 'Excellent and very fast service!') }}"
                                </p>
                            </div>
                            <div class="mt-6 flex items-center justify-between border-t border-outline-variant/10 pt-4">
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-outline/60">{{ $fb->created_at->format('M Y') }}</span>
                                <div
                                    class="flex items-center gap-1.5 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                                    <span class="material-symbols-outlined text-[12px] text-emerald-500">verified</span>
                                    <span
                                        class="text-[8px] font-black uppercase tracking-tighter text-emerald-500">{{ __('Verified') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @foreach($howItWorksSections as $homepageSection)
        @include('storefront.partials.homepage-section', $homepageSection)
    @endforeach

    {{-- FAQ Section --}}
    @if($faqs->isNotEmpty())
        <section class="px-4 md:px-8 py-24 bg-surface-container-lowest/30 relative z-10 sf-reveal-section sf-lazy-section"
            id="faq-section">
            <div class="max-w-4xl mx-auto">
                <x-noir.section-heading :title="__('noir.common_queries')" :centered="true" />

                <div class="space-y-4">
                    @foreach($faqs as $faq)
                        <details class="group bg-surface-container/40 rounded-2xl border border-outline-variant/10 overflow-hidden">
                            <summary
                                class="flex justify-between items-center p-6 cursor-pointer hover:bg-surface-container-high transition-colors list-none">
                                <span
                                    class="font-headline font-bold text-sm md:text-base uppercase tracking-widest text-on-surface">
                                    {{ $faq->{"question_$locale"} }}
                                </span>
                                <span
                                    class="material-symbols-outlined expand-icon transition-transform duration-300 text-primary-container">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 pt-2 animate-fade-in">
                                <p class="text-on-surface-variant text-sm leading-relaxed">
                                    {!! nl2br(e($faq->{"answer_$locale"})) !!}
                                </p>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const revealTargets = document.querySelectorAll('.sf-reveal-section, .glass-panel, #product-grid > *, .sf-product-card');
                if ('IntersectionObserver' in window) {
                    const revealObserver = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) return;
                            entry.target.classList.add('sf-in-view');
                            revealObserver.unobserve(entry.target);
                        });
                    }, { threshold: 0.14, rootMargin: '0px 0px -8% 0px' });

                    revealTargets.forEach((target, index) => {
                        target.classList.add('sf-scroll-reveal');
                        target.style.setProperty('--sf-reveal-delay', `${Math.min(index % 8, 7) * 55}ms`);
                        revealObserver.observe(target);
                    });

                    // IntersectionObserver to pause heavy animations when out of view
                    const animObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.remove('is-paused');
                            } else {
                                entry.target.classList.add('is-paused');
                            }
                        });
                    }, { threshold: 0 });

                    document.querySelectorAll('.animate-marquee, .animate-marquee-rtl').forEach(el => {
                        animObserver.observe(el);
                    });

                } else {
                    revealTargets.forEach((target) => target.classList.add('sf-in-view'));
                }

                // AJAX Category & Filter Switching
                const categoryLinks = document.querySelectorAll('[data-category-link]');
                const productGrid = document.getElementById('product-grid');
                const productsSection = document.getElementById('products-section');

                const setActiveCategory = (activeKey) => {
                    categoryLinks.forEach(link => {
                        const isActive = link.dataset.categoryLink === activeKey;
                        const circle = link.querySelector('[data-category-circle]');
                        const label = link.querySelector('[data-category-label]');
                        const icon = circle?.querySelector('.material-symbols-outlined');
                        const color = link.dataset.categoryColor || '#00f0ff';

                        circle?.classList.toggle('ring-2', isActive);
                        circle?.classList.toggle('border-outline-variant/20', !isActive);
                        label?.classList.toggle('drop-shadow-md', isActive);

                        if (circle) {
                            circle.style.borderColor = isActive ? color : '';
                            circle.style.boxShadow = isActive ? `0 0 22px ${color}38` : '';
                        }
                        if (icon) {
                            icon.style.color = isActive ? color : '';
                        }
                        if (label) {
                            label.style.color = isActive ? color : '';
                        }
                    });
                };

                categoryLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        if (this.getAttribute('href').startsWith('#')) return;

                        e.preventDefault();
                        const url = this.getAttribute('href');
                        const targetUrl = url.split('#')[0];

                        setActiveCategory(this.dataset.categoryLink);

                        productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        productGrid.style.opacity = '0.5';
                        productGrid.style.pointerEvents = 'none';

                        fetch(targetUrl, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(res => res.text())
                            .then(html => {
                                productGrid.innerHTML = html;
                                productGrid.querySelectorAll(':scope > *').forEach((item, index) => {
                                    item.classList.add('sf-scroll-reveal');
                                    item.style.setProperty('--sf-reveal-delay', `${Math.min(index % 8, 7) * 55}ms`);
                                    requestAnimationFrame(() => item.classList.add('sf-in-view'));
                                });
                                productGrid.style.opacity = '1';
                                productGrid.style.pointerEvents = 'auto';
                                history.pushState({ path: url }, '', url);
                                updateLoadMoreButton(targetUrl);
                            })
                            .catch(err => {
                                console.error('Fetch error:', err);
                                productGrid.style.opacity = '1';
                                productGrid.style.pointerEvents = 'auto';
                            });
                    });
                });

                function updateLoadMoreButton(baseUrl) {
                    fetch(baseUrl)
                        .then(r => r.text())
                        .then(fullHtml => {
                            const fullDoc = new DOMParser().parseFromString(fullHtml, 'text/html');
                            const nextBtn = fullDoc.getElementById('load-more-btn');
                            const existingBtn = document.getElementById('load-more-btn');

                            if (nextBtn) {
                                if (existingBtn) {
                                    existingBtn.outerHTML = nextBtn.outerHTML;
                                    attachLoadMoreEvent();
                                } else {
                                    const wrapper = document.createElement('div');
                                    wrapper.className = 'mt-12 flex justify-center';
                                    wrapper.id = 'load-more-wrapper';
                                    wrapper.appendChild(nextBtn);
                                    productsSection.appendChild(wrapper);
                                    attachLoadMoreEvent();
                                }
                            } else if (existingBtn) {
                                const wrapper = document.getElementById('load-more-wrapper') || existingBtn.parentElement;
                                wrapper.remove();
                            }
                        });
                }

                function attachLoadMoreEvent() {
                    const loadMoreBtn = document.getElementById('load-more-btn');
                    if (!loadMoreBtn) return;

                    loadMoreBtn.addEventListener('click', function () {
                        const url = this.dataset.url;
                        loadMoreBtn.disabled = true;
                        loadMoreBtn.querySelector('span:last-child').innerText = '{{ $locale == "ar" ? "جاري التحميل..." : "Loading..." }}';

                        fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(res => res.text())
                            .then(html => {
                                const temp = document.createElement('div');
                                temp.innerHTML = html;
                                const items = temp.querySelectorAll('.animate-fade-in');
                                items.forEach((item, index) => {
                                    item.classList.add('sf-scroll-reveal');
                                    item.style.setProperty('--sf-reveal-delay', `${Math.min(index % 8, 7) * 55}ms`);
                                    productGrid.appendChild(item);
                                    requestAnimationFrame(() => item.classList.add('sf-in-view'));
                                });

                                fetch(url)
                                    .then(r => r.text())
                                    .then(fullHtml => {
                                        const fullDoc = new DOMParser().parseFromString(fullHtml, 'text/html');
                                        const nextBtn = fullDoc.getElementById('load-more-btn');
                                        if (nextBtn) {
                                            loadMoreBtn.dataset.url = nextBtn.dataset.url;
                                            loadMoreBtn.disabled = false;
                                            loadMoreBtn.querySelector('span:last-child').innerText = '{{ $locale == "ar" ? "عرض المزيد" : "Load More" }}';
                                        } else {
                                            const wrapper = document.getElementById('load-more-wrapper') || loadMoreBtn.parentElement;
                                            wrapper.remove();
                                        }
                                    });
                            })
                            .catch(() => {
                                loadMoreBtn.disabled = false;
                                loadMoreBtn.querySelector('span:last-child').innerText = '{{ $locale == "ar" ? "عرض المزيد" : "Load More" }}';
                            });
                    });
                }

                attachLoadMoreEvent();

                const sharedParams = new URLSearchParams(window.location.search);
                const sharedSubcategory = sharedParams.get('subcategory');
                const sharedProduct = sharedParams.get('product');
                if (sharedSubcategory && window.openSubcategoryModal) {
                    window.openSubcategoryModal(sharedSubcategory, sharedProduct ? Number(sharedProduct) : null);
                }

                // Carousel Logic
                const carousel = document.getElementById('hero-carousel');
                if (carousel) {
                    const inner = carousel.querySelector('.carousel-inner');
                    const items = carousel.querySelectorAll('.carousel-item');
                    const indicators = carousel.querySelectorAll('.carousel-indicator');
                    let currentIndex = 0;
                    const totalSlides = items.length;
                    let autoSlideId = null;
                    let carouselInView = true;

                    function updateCarousel() {
                        inner.style.transform = `translateX(-${currentIndex * 100}%)`;
                        indicators.forEach((ind, i) => {
                            const isActive = i === currentIndex;
                            ind.classList.toggle('bg-primary-container', isActive);
                            ind.classList.toggle('border-primary-container', isActive);
                            ind.classList.toggle('w-3', isActive);
                            ind.classList.toggle('w-2', !isActive);
                        });

                        // Synchronize CTA buttons
                        const ctas = document.querySelectorAll('.banner-cta-item');
                        ctas.forEach((cta, i) => {
                            cta.classList.toggle('hidden', i !== currentIndex);
                        });
                    }

                    function stopAutoSlide() {
                        if (autoSlideId !== null) {
                            window.clearInterval(autoSlideId);
                            autoSlideId = null;
                        }
                    }

                    function startAutoSlide() {
                        if (autoSlideId !== null || totalSlides <= 1 || !carouselInView || document.hidden) {
                            return;
                        }

                        autoSlideId = window.setInterval(() => {
                            window.nextSlide();
                        }, 5000);
                    }

                    window.nextSlide = function () {
                        const isRtl = document.documentElement.dir === 'rtl';
                        currentIndex = isRtl
                            ? (currentIndex - 1 + totalSlides) % totalSlides
                            : (currentIndex + 1) % totalSlides;
                        updateCarousel();
                    };

                    window.prevSlide = function () {
                        const isRtl = document.documentElement.dir === 'rtl';
                        currentIndex = isRtl
                            ? (currentIndex + 1) % totalSlides
                            : (currentIndex - 1 + totalSlides) % totalSlides;
                        updateCarousel();
                    };

                    indicators.forEach(ind => {
                        ind.addEventListener('click', function () {
                            currentIndex = parseInt(this.dataset.index);
                            updateCarousel();
                        });
                    });

                    if ('IntersectionObserver' in window) {
                        const carouselObserver = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                carouselInView = entry.isIntersecting;
                                if (carouselInView) {
                                    startAutoSlide();
                                } else {
                                    stopAutoSlide();
                                }
                            });
                        }, { threshold: 0.25 });

                        carouselObserver.observe(carousel);
                    }

                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            stopAutoSlide();
                        } else {
                            startAutoSlide();
                        }
                    });

                    carousel.addEventListener('mouseenter', stopAutoSlide);
                    carousel.addEventListener('mouseleave', startAutoSlide);

                    // Swipe Support
                    let touchStartX = 0;
                    let touchEndX = 0;

                    carousel.addEventListener('touchstart', e => {
                        touchStartX = e.changedTouches[0].screenX;
                        stopAutoSlide();
                    }, { passive: true });

                    carousel.addEventListener('touchend', e => {
                        touchEndX = e.changedTouches[0].screenX;
                        handleSwipe();
                        startAutoSlide();
                    }, { passive: true });

                    function handleSwipe() {
                        const swipeThreshold = 50;
                        const diff = touchEndX - touchStartX;
                        if (Math.abs(diff) < swipeThreshold) return;

                        const isRtl = document.documentElement.dir === 'rtl';
                        if (diff > 0) {
                            // Swiped Right
                            isRtl ? window.nextSlide() : window.prevSlide();
                        } else {
                            // Swiped Left
                            isRtl ? window.prevSlide() : window.nextSlide();
                        }
                    }

                    startAutoSlide();
                    updateCarousel();
                }
            });
        </script>
    @endpush
@endsection