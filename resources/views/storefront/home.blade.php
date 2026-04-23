@extends('storefront.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'MeaCash - بطاقات رقمية وشحن ألعاب' : 'MeaCash - Digital Cards & Game Top-ups')

@section('content')
    @php $locale = app()->getLocale(); @endphp
    @php $activeSearchQuery = trim((string) ($searchQuery ?? request('q', ''))); @endphp
    @php
        $homepageSections = $homepageSections ?? collect();
        $contentHomepageSections = $homepageSections->filter(fn ($payload) => $payload['section']->isContentBlock());
        $cryptoHomepageSections = $contentHomepageSections->filter(fn ($payload) => $payload['section']->type === \App\Models\HomepageSection::TYPE_CRYPTO_CARD);
        $productHomepageSections = $homepageSections->reject(fn ($payload) => $payload['section']->isContentBlock());
        $firstBanner = $banners->first();
        $firstBannerDesktopUrl = $firstBanner?->imageUrl();
        $firstBannerMobileUrl = $firstBanner?->mobileImageUrl();
        $firstBannerPreloadUrl = $firstBannerMobileUrl ?: $firstBannerDesktopUrl;
        $firstBannerPreloadSrcset = $firstBannerMobileUrl && $firstBannerDesktopUrl && $firstBannerMobileUrl !== $firstBannerDesktopUrl
            ? $firstBannerMobileUrl.' 768w, '.$firstBannerDesktopUrl.' 1440w'
            : null;
    @endphp

    @if($firstBannerDesktopUrl)
        @push('styles')
            <link rel="preload"
                as="image"
                href="{{ $firstBannerPreloadUrl }}"
                @if($firstBannerPreloadSrcset)
                    imagesrcset="{{ $firstBannerPreloadSrcset }}"
                    imagesizes="100vw"
                @endif
                fetchpriority="high">
        @endpush
    @endif

    <div class="sf-home-atmosphere" aria-hidden="true">
        <div class="sf-home-orb sf-home-orb-cyan"></div>
        <div class="sf-home-orb sf-home-orb-magenta"></div>
        <div class="sf-home-dot-field"></div>
        <div class="sf-home-square-field">
            @for($i = 0; $i < 18; $i++)
                <span style="--i: {{ $i }};"></span>
            @endfor
        </div>
    </div>

    {{-- Hero Banner Carousel Section --}}
    <section class="relative px-4 md:px-8 pt-6 pb-6 z-10 sf-reveal-section">
        <div class="mc-hero-shell mx-auto grid w-full max-w-[1440px] overflow-hidden rounded-[24px] shadow-2xl lg:grid-cols-[0.9fr_1.65fr_0.9fr] lg:rounded-[32px]">
            <aside class="mc-hero-side-panel mc-hero-side-shop hidden min-h-[420px] flex-col justify-between p-8 lg:flex">
                <div>
                <p class="font-headline text-3xl font-light text-on-surface">{{ __('Shop') }}</p>
                    <h2 class="mt-2 max-w-xs font-headline text-4xl font-black leading-tight text-on-surface">{{ __('Digital Products') }}</h2>
                    <p class="mt-4 text-sm text-on-surface-variant">{{ __('Fast delivery · Secure checkout · Best deals') }}</p>
                    <a href="#products-section" class="mt-6 inline-flex rounded-full bg-primary-container px-7 py-3 font-headline text-xs font-black uppercase tracking-widest text-on-primary-container shadow-lg transition hover:scale-105">
                        {{ __('Explore Store') }}
                    </a>
                </div>
                <div class="mc-hero-product-stage" aria-hidden="true">
                    <x-noir.logo
                        alt=""
                        class="mc-logo"
                        sizes="176px" />
                    <span class="material-symbols-outlined">sports_esports</span>
                    <span class="material-symbols-outlined">redeem</span>
                    <span class="material-symbols-outlined">bolt</span>
                </div>
            </aside>

            <div id="hero-carousel"
                class="group relative h-[425px] w-full overflow-hidden rounded-[24px] shadow-2xl sm:h-[500px] lg:h-[420px] lg:rounded-none lg:shadow-none xl:h-[460px]">
                <div class="carousel-inner h-full w-full flex transition-transform duration-700 ease-in-out" dir="ltr">
                    @forelse($banners as $banner)
                        <div class="carousel-item min-w-full h-full relative sf-skeleton">
                            <picture>
                                <source media="(max-width: 768px)" srcset="{{ $banner->mobileImageUrl() }}">
                                <img class="w-full h-full object-cover sf-img-loading"
                                    src="{{ $banner->imageUrl() }}"
                                    alt="{{ $banner->{"title_$locale"} }}"
                                    width="1440" height="720"
                                    sizes="100vw"
                                    fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="{{ $loop->first ? 'sync' : 'async' }}"
                                    onload="this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');">
                            </picture>
                            <div
                                class="mc-carousel-overlay absolute inset-0 bg-gradient-to-t md:bg-gradient-to-s from-background via-background/20 to-transparent">
                            </div>

                            <div class="absolute inset-0 flex items-center p-6 sm:p-10 md:p-16 lg:p-10 xl:p-14">
                                <div class="max-w-2xl">
                                    <h1
                                        class="font-headline px-2 text-4xl font-black italic leading-[1.1] tracking-tighter sm:text-5xl lg:text-5xl xl:text-6xl mb-4 animate-fade-in-up sf-text-gradient">
                                        {{ $banner->{"title_$locale"} }}
                                    </h1>
                                    <p
                                        class="text-on-surface-variant text-base md:text-xl lg:text-base xl:text-lg max-w-lg leading-relaxed animate-fade-in-up-delay">
                                        {{ $banner->{"description_$locale"} }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="carousel-item min-w-full h-full relative">
                            <img class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDrlznlKgHHa63HOhVKSNWE8C5o6YWGzqxbSrsVKR6imUOq2BDzZoRlqJg_aBtStZO89zUqnzPz4cUR1Ar_9KPYAsyplUSUhl7Cu69sWYscBbkmZv8_Z23wFHRJUsaHoWrCgTg_AZAPtY_FpHMiau3uk0SCMp2vwzAl9Sk5ydgPkW2up5bhPyu8FmcOIpMoaTLYNwC-ofII6e2sndmu9_tc47MTiFoRRkToqSy-lC4CowcwR89nZBqQxnz4mrEdSPnNMxpTJO40tQ"
                                alt="Default Hero Image" width="1440" height="720" fetchpriority="high" loading="eager" decoding="sync">
                            <div class="mc-carousel-overlay absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>
                        </div>
                    @endforelse
                </div>

                {{-- Carousel Hub: Indicators & Global CTA --}}
                @if($banners->count() > 0)
                    <div
                        class="absolute bottom-6 sm:bottom-10 right-6 sm:right-10 flex flex-col-reverse sm:flex-row items-center gap-4 sm:gap-8 z-30">
                        <div class="flex gap-2.5">
                            @foreach($banners as $index => $banner)
                                <button
                                    class="carousel-indicator w-2 h-2 rounded-full border border-white/20 transition-all hover:scale-125 {{ $index === 0 ? 'bg-primary-container border-primary-container w-6' : '' }}"
                                    data-index="{{ $index }}"
                                    aria-label="{{ __('Show banner') }} {{ $index + 1 }}"></button>
                            @endforeach
                        </div>

                        <div id="banner-cta-container">
                            @foreach($banners as $index => $banner)
                                <div class="banner-cta-item {{ $index === 0 ? '' : 'hidden' }}" data-index="{{ $index }}">
                                    <x-noir.button variant="primary" href="{{ $banner->link }}" icon="bolt"
                                        class="px-4 py-2.5 sm:px-7 sm:py-3.5 text-[9px] sm:text-xs min-w-[120px]">
                                        {{ $banner->{"button_text_$locale"} ?? __('noir.claim_now') }}
                                    </x-noir.button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button
                        class="absolute top-1/2 left-6 -translate-y-1/2 w-12 h-12 rounded-full glass-panel hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white/10"
                        onclick="prevSlide()"
                        aria-label="{{ __('Previous banner') }}">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button
                        class="absolute top-1/2 right-6 -translate-y-1/2 w-12 h-12 rounded-full glass-panel hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white/10"
                        onclick="nextSlide()"
                        aria-label="{{ __('Next banner') }}">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                @endif
            </div>

            <aside class="mc-hero-side-panel mc-hero-side-social hidden min-h-[420px] flex-col items-center justify-center p-8 text-center lg:flex">
                <p class="font-label text-[10px] font-black uppercase tracking-[0.32em] text-on-surface-variant">{{ __('Join us on') }}</p>
                <div class="my-8 flex h-36 w-36 items-center justify-center rounded-[2rem] border border-outline-variant/20 bg-surface-container-low/45">
                    <span class="font-headline text-8xl font-black leading-none text-on-surface">X</span>
                </div>
                <p class="max-w-xs text-sm leading-relaxed text-on-surface-variant">{{ __('Follow the latest products, offers, and updates.') }}</p>
                <a href="{{ $contactUrl ?? route('store.contact') }}" class="mt-6 font-label text-[10px] font-black uppercase tracking-[0.24em] text-primary-container">
                    @MEACASH
                </a>
            </aside>
        </div>
    </section>

    {{-- High-Fidelity Infinite Brand Image Strip --}}
        @php
            $brandTiles = $featuredSubcategories->count() > 0 
                ? ($featuredSubcategories->count() < 10 ? $featuredSubcategories->merge($featuredSubcategories) : $featuredSubcategories) 
                : collect();
        @endphp
        <div class="overflow-hidden py-3 sf-lazy-section">
            <div class="{{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }} flex w-max items-center gap-3 md:gap-4 py-2" dir="ltr">
                @foreach($brandTiles as $sub)
                    @php
                        $subImage = $sub->image ? (str_starts_with($sub->image, 'http') ? $sub->image : \Illuminate\Support\Facades\Storage::url($sub->image)) : asset('meacash-logo-128.png');
                        $subName = $sub->{"name_$locale"} ?: $sub->name_en;
                    @endphp
                    <div onclick="window.openSubcategoryModal('{{ $sub->slug }}')"
                        class="sf-brand-card group flex h-28 w-28 md:h-32 md:w-32 shrink-0 flex-col items-center justify-center rounded-2xl border border-outline-variant/10 bg-surface-container/55 p-4 transition-all duration-300 hover:border-primary-container/70 hover:bg-surface-container-high hover:shadow-[0_0_30px_rgba(0,240,255,0.12)] cursor-pointer">
                        <div class="sf-brand-card-icon-shell mb-3 flex h-12 w-12 items-center justify-center rounded-2xl">
                            <img src="{{ $subImage }}" alt="{{ $subName }}" loading="lazy" width="36" height="36" decoding="async"
                                class="sf-brand-card-icon h-9 w-9 object-contain opacity-90 transition-all duration-300 group-hover:scale-110 group-hover:opacity-100"
                                onerror="this.src='{{ asset('meacash-logo-128.png') }}';">
                        </div>
                        <span
                            class="sf-brand-card-label w-full truncate text-center font-headline text-[9px] font-black uppercase tracking-widest text-on-surface/45 transition-colors group-hover:text-primary-container">
                            {{ $subName }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Unified Infinite Brand Marquee --}}
    <section
        class="sf-brand-marquee w-full py-8 border-y border-outline-variant/10 bg-surface-container-lowest/50 backdrop-blur-sm overflow-hidden z-10 relative sf-reveal-section sf-lazy-section">
        <div class="flex items-center gap-16 md:gap-32 w-max {{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }}" dir="ltr">
            @php
                $marqueeItems = $featuredSubcategories->count() > 0 
                    ? ($featuredSubcategories->count() < 10 ? $featuredSubcategories->merge($featuredSubcategories)->merge($featuredSubcategories) : $featuredSubcategories) 
                    : collect();
            @endphp
            @foreach($marqueeItems as $sub)
                @php
                    $subImage = $sub->image ? (str_starts_with($sub->image, 'http') ? $sub->image : \Illuminate\Support\Facades\Storage::url($sub->image)) : asset('meacash-logo-128.png');
                    $subName = $sub->{"name_$locale"} ?: $sub->name_en;
                @endphp
                <div onclick="window.openSubcategoryModal('{{ $sub->slug }}')"
                    class="sf-brand-marquee-item flex items-center gap-3 group/brand cursor-pointer opacity-70 hover:opacity-100 transition-all duration-500 shrink-0">
                    <img src="{{ $subImage }}" alt="{{ $subName }}" loading="lazy" width="28" height="28" decoding="async"
                        class="sf-brand-marquee-icon h-7 w-7 object-contain transition-transform group-hover/brand:scale-110"
                        onerror="this.src='{{ asset('meacash-logo-128.png') }}';">
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
                    <a href="{{ route('store.home', ['featured' => 1]) }}#products-section"
                        data-category-link="hot" data-category-color="#fe00fe"
                        class="sf-category-item flex flex-col items-center gap-4 shrink-0 group">
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

                    <a href="{{ route('store.home') }}#products-section" data-category-link="all"
                        data-category-color="#fbbf24" class="sf-category-item flex flex-col items-center gap-4 shrink-0 group">
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
        <section class="py-16 md:py-24 border-y border-outline-variant/10 bg-surface-container-low/30 relative overflow-hidden sf-reveal-section sf-lazy-section">
            <div class="sf-home-atmosphere opacity-20" aria-hidden="true">
                <div class="sf-home-orb sf-home-orb-magenta" style="left: 80%; top: 20%;"></div>
            </div>
            
            <div class="px-4 md:px-8 mb-12">
                <x-noir.section-heading :title="__('What Our Community Says')" :subtitle="__('User Reviews')" :centered="true" :gradient="true" />
            </div>

            <div class="overflow-hidden">
                <div class="{{ $locale === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }} flex w-max items-stretch gap-6 px-10" dir="ltr">
                    @php
                        $feedbackCards = $featuredFeedbacks->count() < 6 ? $featuredFeedbacks->concat($featuredFeedbacks) : $featuredFeedbacks;
                    @endphp
                    @foreach($feedbackCards as $fb)
                        <div class="sf-feedback-card group w-72 md:w-96 p-6 rounded-3xl border border-outline-variant/15 bg-surface-container/60 backdrop-blur-md flex flex-col justify-between transition-all duration-500 hover:border-primary-container/40 hover:bg-surface-container-high hover:shadow-[0_0_40px_rgba(0,240,255,0.08)]">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-container/20 to-secondary-container/20 border border-outline-variant/30 flex items-center justify-center font-headline font-black text-primary-container">
                                            {{ substr($fb->user?->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-headline text-sm font-black text-on-surface">{{ $fb->user?->name ?? 'Anonymous' }}</div>
                                            <div class="text-[9px] uppercase tracking-widest text-outline">{{ __('Verified Buyer') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex gap-0.5 text-amber-500">
                                        @for($i = 0; $i < 5; $i++)
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' {{ $i < ($fb->rating ?? 5) ? 1 : 0 }}">star</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm italic leading-relaxed text-on-surface-variant line-clamp-4">
                                    "{{ $fb->comment ?: ($locale === 'ar' ? 'خدمة رائعة وسريعة جداً!' : 'Excellent and very fast service!') }}"
                                </p>
                            </div>
                            <div class="mt-6 flex items-center justify-between border-t border-outline-variant/10 pt-4">
                                <span class="text-[9px] font-black uppercase tracking-widest text-outline/60">{{ $fb->created_at->format('M Y') }}</span>
                                <div class="flex items-center gap-1.5 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                                    <span class="material-symbols-outlined text-[12px] text-emerald-500">verified</span>
                                    <span class="text-[8px] font-black uppercase tracking-tighter text-emerald-500">{{ __('Verified') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(false)
    {{-- Static Why MeaCash Section --}}
    <section class="px-4 md:px-8 py-16 md:py-20 relative z-10 sf-reveal-section sf-lazy-section">
        <div class="mx-auto max-w-7xl rounded-[2rem] border border-outline-variant/15 bg-surface-container-low/55 p-6 shadow-2xl backdrop-blur-md md:p-10">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.4fr] lg:items-center">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-primary-container/25 bg-primary-container/10 px-3 py-1 font-label text-[10px] font-black uppercase tracking-[0.24em] text-primary-container">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        {{ $locale === 'ar' ? 'ثقة وسرعة' : 'Trust & Speed' }}
                    </span>
                    <h2 class="mt-5 font-headline text-3xl font-black uppercase tracking-tight text-on-surface md:text-5xl">
                        {{ $locale === 'ar' ? 'لماذا MeaCash؟' : 'Why MeaCash?' }}
                    </h2>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-on-surface-variant md:text-base">
                        {{ $locale === 'ar'
                            ? 'نساعدك على شراء البطاقات الرقمية وشحن الألعاب والخدمات الإلكترونية بسرعة، مع محفظة واضحة ودعم محلي عندما تحتاجه.'
                            : 'We help you buy digital cards, game top-ups, and online services quickly, with a clear wallet experience and local support when you need it.' }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach([
                        ['icon' => 'bolt', 'en' => 'Fast Delivery', 'ar' => 'تسليم سريع', 'text_en' => 'Orders are prepared for quick digital fulfillment.', 'text_ar' => 'تتم معالجة الطلبات لتسليم رقمي سريع.'],
                        ['icon' => 'account_balance_wallet', 'en' => 'Wallet Checkout', 'ar' => 'الدفع بالمحفظة', 'text_en' => 'Top up once and use your balance for repeat purchases.', 'text_ar' => 'اشحن مرة واحدة واستخدم الرصيد لطلباتك المتكررة.'],
                        ['icon' => 'shield', 'en' => 'Verified Products', 'ar' => 'منتجات موثوقة', 'text_en' => 'Products and packages are managed with approved suppliers.', 'text_ar' => 'تتم إدارة المنتجات والباقات مع موردين معتمدين.'],
                        ['icon' => 'support_agent', 'en' => 'Local Support', 'ar' => 'دعم محلي', 'text_en' => 'Our team follows wallet top-ups, orders, and reports.', 'text_ar' => 'فريقنا يتابع شحن المحفظة والطلبات والبلاغات.'],
                    ] as $item)
                        <div class="rounded-[1.35rem] border border-outline-variant/15 bg-surface-container/70 p-5 transition hover:border-primary-container/45 hover:bg-surface-container-high">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl border border-primary-container/20 bg-primary-container/10 text-primary-container">
                                <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                            </div>
                            <h3 class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">
                                {{ $locale === 'ar' ? $item['ar'] : $item['en'] }}
                            </h3>
                            <p class="mt-2 text-xs leading-relaxed text-on-surface-variant">
                                {{ $locale === 'ar' ? $item['text_ar'] : $item['text_en'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Section --}}
    <section class="px-4 md:px-8 py-16 md:py-20 relative sf-reveal-section sf-lazy-section">
        <div class="grid grid-cols-12 gap-8 items-center max-w-7xl mx-auto">
            <div class="col-span-12 lg:col-span-5 order-2 lg:order-1 text-start">
                <h2 class="font-headline text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-8 uppercase">
                    {{ __('noir.secure_assets_title') }} <br /><span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f0ff] to-[#fe00fe]">{{ __('noir.secure_assets_subtitle') }}</span>
                </h2>
                <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-10">
                    {{ __('noir.secure_assets_desc') }}
                </p>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-primary-container/10 flex items-center justify-center border border-primary-container/20">
                            <span class="material-symbols-outlined text-primary-container">speed</span>
                        </div>
                        <div>
                            <h4 class="font-headline text-sm font-bold uppercase tracking-widest text-on-surface">Ultra Fast
                                Delivery</h4>
                            <p class="text-xs text-on-surface-variant/60">Average delivery time: 14 seconds</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-secondary-container/10 flex items-center justify-center border border-secondary-container/20">
                            <span class="material-symbols-outlined text-secondary-container">security</span>
                        </div>
                        <div>
                            <h4 class="font-headline text-sm font-bold uppercase tracking-widest text-on-surface">Vanguard
                                Security</h4>
                            <p class="text-xs text-on-surface-variant/60">Military grade encryption protocols</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-7 order-1 lg:order-2 flex justify-end">
                <div class="relative w-full aspect-video rounded-3xl overflow-hidden glass-panel p-1">
                    <img class="w-full h-full object-cover rounded-2xl"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRQAeS2ZAoyahfL86240LJ26Ih0F4jliE9yad411iOINSt87VdX-bC4PHrU3QM1Adrs0iP357z99aoZCjSU3nFCwUWmTM_s2eT7SuL7kOi_DKdnxIM6GIOY-2X_tF4VSNlfVXvriKPLiCfXsKRvkQWvt859u-jl6ttPOLclU_DS4fM30A_DKjIq0EMg9EYY-npcbrfJe_2EYoFqxxmkHvPmM8_zgN48QvwTGRH6HlTqawlZ2Zy8GgGWkv24yDT8m5RKMpWX3x4Nd4"
                        alt="Security Visualization" width="1280" height="720" loading="lazy" decoding="async">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-primary-container/20 to-transparent pointer-events-none">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @foreach($howItWorksSections as $homepageSection)
        @include('storefront.partials.homepage-section', $homepageSection)
    @endforeach
    @endif

    {{-- FAQ Section --}}
    @if($faqs->isNotEmpty())
        <section class="px-4 md:px-8 py-24 bg-surface-container-lowest/30 relative z-10 sf-reveal-section sf-lazy-section" id="faq-section">
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
                    
                    document.querySelectorAll('.animate-marquee, .animate-marquee-rtl, .sf-home-atmosphere').forEach(el => {
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
                            ind.classList.toggle('w-6', isActive);
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
                    carousel.addEventListener('touchstart', stopAutoSlide, { passive: true });
                    carousel.addEventListener('touchend', startAutoSlide, { passive: true });

                    startAutoSlide();
                    updateCarousel();
                }
            });
        </script>
    @endpush
@endsection
