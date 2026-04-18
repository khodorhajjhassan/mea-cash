@extends('storefront.layouts.app')
 
@section('title', app()->getLocale() == 'ar' ? 'MeaCash - بطاقات رقمية وشحن ألعاب' : 'MeaCash - Digital Cards & Game Top-ups')
 
@section('content')
@php $locale = app()->getLocale(); @endphp
@php $activeSearchQuery = trim((string) ($searchQuery ?? request('q', ''))); @endphp
 
{{-- Hero Section (Dynamic Banner Carousel) --}}
<section class="py-6 lg:py-10">
    @if($banners->isNotEmpty())
        <div class="relative overflow-hidden rounded-[32px] bg-slate-900 shadow-2xl" id="hero-carousel">
            <div class="carousel-inner flex transition-transform duration-700 ease-in-out">
                @foreach($banners as $banner)
                <div class="carousel-item relative min-w-full aspect-[21/9] flex items-center overflow-hidden">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('media.disk'))->url($banner->image_path) }}" 
                         alt="{{ $banner->title_en }}" 
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/40 to-transparent"></div>
                    
                    <div class="relative z-10 px-8 md:px-16 w-full max-w-2xl animate-fade-in-up">
                        <div class="sf-eyebrow mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $locale == 'ar' ? 'عرض حصري' : 'Exclusive Deal' }}
                        </div>
                        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight">
                            {{ $banner->{"title_{$locale}"} }}
                        </h2>
                        @if($banner->{"description_{$locale}"})
                        <p class="text-slate-300 mb-8 max-w-lg hidden md:block">
                            {{ $banner->{"description_{$locale}"} }}
                        </p>
                        @endif
                        @if($banner->link)
                        <a href="{{ $banner->link }}" class="sf-btn-cyan">
                            {{ $banner->{"button_text_{$locale}"} ?: ($locale == 'ar' ? 'اطلب الآن' : 'Order Now') }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($banners->count() > 1)
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                @foreach($banners as $idx => $b)
                <button class="carousel-dot w-2 h-2 rounded-full bg-white/30 transition-all" data-index="{{ $idx }}"></button>
                @endforeach
            </div>
            @endif
        </div>
    @else
        <div class="sf-panel sf-hero relative overflow-hidden" style="padding: 1.5rem 1rem;">
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
                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <a href="#products-section" class="sf-btn-cyan w-full sm:w-auto">
                        {{ $locale == 'ar' ? 'تسوق الآن' : 'Shop Now' }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</section>

{{-- Top Deals (Featured Subcategories) --}}
@php $featuredSubs = \App\Models\Subcategory::where('is_featured', true)->where('is_active', true)->take(6)->get(); @endphp
@if($featuredSubs->isNotEmpty())
<section class="py-6 sm:py-10">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-pink-500 border border-pink-500/20">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">{{ $locale == 'ar' ? 'أفضل العروض' : 'Top Deals' }}</h2>
            <p class="text-[10px] md:text-xs text-slate-500 uppercase tracking-widest font-bold">{{ $locale == 'ar' ? 'منتجات مختارة لك' : 'Handpicked for you' }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($featuredSubs as $sub)
        <a href="{{ route('store.home', ['subcategory' => $sub->slug]) }}#products-section" data-subcategory-slug="{{ $sub->slug }}" class="group relative overflow-hidden rounded-[24px] bg-slate-900/40 border border-white/5 p-5 hover:border-cyan-400/50 hover:bg-slate-900/60 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-400/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 rounded-2xl bg-slate-950/50 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-inner">
                    <img src="{{ $sub->image ?? 'https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://' . Str::slug($sub->name_en) . '.com&size=128' }} " 
                         alt="" class="w-10 h-10 object-contain rounded-lg" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($sub->name_en) }}&background=0D1117&color=00D4FF'">
                </div>
                <h3 class="text-sm font-bold text-white group-hover:text-cyan-400 transition-colors">{{ $sub->{"name_{$locale}"} }}</h3>
                <span class="mt-1 text-[10px] text-slate-500 font-medium">{{ $sub->category->{"name_{$locale}"} }}</span>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

<div class="sf-marquee-container">
    <div class="sf-marquee-content sf-animate-marquee">
        @php
            $brands = [
                ['name' => 'PlayStation', 'domain' => 'playstation.com'],
                ['name' => 'Xbox', 'domain' => 'xbox.com'],
                ['name' => 'Steam', 'domain' => 'steampowered.com'],
                ['name' => 'Disney+', 'domain' => 'disneyplus.com'],
                ['name' => 'Netflix', 'domain' => 'netflix.com'],
                ['name' => 'Apple', 'domain' => 'apple.com'],
                ['name' => 'Google', 'domain' => 'google.com'],
                ['name' => 'Amazon', 'domain' => 'amazon.com'],
                ['name' => 'Microsoft', 'domain' => 'microsoft.com'],
                ['name' => 'Adobe', 'domain' => 'adobe.com'],
                ['name' => 'Nintendo', 'domain' => 'nintendo.com'],
                ['name' => 'Deezer', 'domain' => 'deezer.com'],
                ['name' => 'Epic Games', 'domain' => 'epicgames.com'],
                ['name' => 'Roblox', 'domain' => 'roblox.com'],
                ['name' => 'Twitch', 'domain' => 'twitch.tv'],
                ['name' => 'Discord', 'domain' => 'discord.com'],
                ['name' => 'Shopify', 'domain' => 'shopify.com'],
                ['name' => 'Zoom', 'domain' => 'zoom.us'],
                ['name' => 'Slack', 'domain' => 'slack.com'],
                ['name' => 'Canva', 'domain' => 'canva.com'],
                ['name' => 'Spotify', 'domain' => 'spotify.com'],
            ];
            // Render 3 times to ensure no gaps during long animations
            $repeatedBrands = array_merge($brands, $brands, $brands);
        @endphp
        @foreach($repeatedBrands as $brand)
        <div class="sf-marquee-item">
            <img src="https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://{{ $brand['domain'] }}&size=128" 
                 alt="{{ $brand['name'] }}" 
                 class="h-8 w-8 object-contain">
        </div>
        @endforeach
    </div>
</div>
 
{{-- Category Bar --}}
@if($categories->isNotEmpty())
<div class="sf-category-bar" id="sf-category-bar">
    <div class="sf-category-scroll" id="category-scroll">
        <a href="{{ route('store.home') }}#products-section" class="sf-cat-orb {{ !request('category') && !request('featured') ? 'active' : '' }}">
            <span class="sf-cat-orb-icon">✨</span>
            <span class="sf-cat-orb-label">{{ $locale == 'ar' ? 'الكل' : 'All' }}</span>
        </a>
        <a href="{{ route('store.home', ['featured' => 1]) }}#products-section" class="sf-cat-orb {{ request('featured') ? 'active' : '' }}">
            <span class="sf-cat-orb-icon">🔥</span>
            <span class="sf-cat-orb-label">{{ $locale == 'ar' ? 'أفضل العروض' : 'Top Deals' }}</span>
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('store.home', ['category' => $cat->slug]) }}#products-section" class="sf-cat-orb {{ request('category') == $cat->slug ? 'active' : '' }}">
            <span class="sf-cat-orb-icon">{{ $cat->icon ?: '💎' }}</span>
            <span class="sf-cat-orb-label">{{ $cat->{"name_{$locale}"} }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif
 
{{-- Products Grid --}}
<section id="products-section" class="py-8">
    <div class="mb-8 flex items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold sm:text-3xl font-heading text-white">
                {{ $activeSearchQuery !== '' ? ($locale == 'ar' ? 'نتائج البحث' : 'Search Results') : ($locale == 'ar' ? 'اكتشف منتجاتنا' : 'Discover Our Products') }}
            </h2>
            <div class="mt-2 h-1 w-20 bg-gradient-to-r from-cyan-400 to-pink-500 rounded-full"></div>
        </div>
        @if($activeSearchQuery !== '')
            <a href="{{ route('store.home') }}" class="sf-btn-outline" style="height: 2.5rem; font-size: 0.875rem;">
                {{ $locale == 'ar' ? 'مسح البحث' : 'Clear Search' }}
            </a>
        @endif
    </div>
 
    <div id="product-grid" class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 xl:gap-6">
        @include('storefront.partials.product-grid-items', ['products' => $products])
    </div>
 
    {{-- Pagination / Load More --}}
    @if($products->hasMorePages())
    <div class="mt-12 flex justify-center">
        <button id="load-more-btn" data-url="{{ $products->nextPageUrl() }}" class="sf-btn-outline group min-w-[12rem]">
            <span>{{ $locale == 'ar' ? 'عرض المزيد' : 'Load More' }}</span>
            <svg class="w-4 h-4 group-hover:translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>
    @endif
 
    @if($products->isEmpty())
    <div class="sf-panel p-12 text-center" style="border-radius: var(--sf-radius-lg);">
        <div class="text-4xl mb-4">🔍</div>
        <p class="text-xl font-bold text-white">
            {{ $locale == 'ar' ? 'لا توجد منتجات حالياً' : 'No products available' }}
        </p>
        <p class="mt-2 text-slate-400">
            {{ $locale == 'ar' ? 'جرّب فئة أخرى أو عد لاحقاً.' : 'Try another category or come back later.' }}
        </p>
    </div>
    @endif
</section>
 
{{-- FAQ Section --}}
@if($faqs->isNotEmpty())
<section class="py-12" id="faq-section">
    <div class="sf-panel p-8 rounded-[32px]">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-4xl font-bold text-white mb-4">
                {{ $locale == 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions' }}
            </h2>
            <p class="text-slate-400">{{ $locale == 'ar' ? 'إليك كل ما تحتاج لمعرفته حول خدماتنا' : 'Everything you need to know about our services' }}</p>
        </div>
 
        <div class="max-w-4xl mx-auto space-y-4">
            @foreach($faqs as $faq)
            <div class="faq-item sf-panel bg-slate-900/50 hover:bg-slate-900 transition-colors border-slate-800 cursor-pointer overflow-hidden">
                <div class="faq-trigger flex items-center justify-between p-5 md:p-6 select-none">
                    <span class="text-base md:text-lg font-semibold text-slate-200">
                        {{ $faq->{"question_{$locale}"} }}
                    </span>
                    <svg class="w-5 h-5 text-cyan-400 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="faq-content max-h-0 transition-all duration-300 ease-in-out opacity-0">
                    <div class="p-6 pt-0 text-slate-400 leading-relaxed border-t border-slate-800/50">
                        {!! nl2br(e($faq->{"answer_{$locale}"})) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
 
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carousel Logic
    const carousel = document.getElementById('hero-carousel');
    if (carousel) {
        const inner = carousel.querySelector('.carousel-inner');
        const dots = carousel.querySelectorAll('.carousel-dot');
        let current = 0;
        const total = {{ $banners->count() }};
 
        function showSlide(idx) {
            inner.style.transform = `translateX(-${idx * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white/100', i === idx);
                dot.classList.toggle('w-6', i === idx);
                dot.classList.toggle('bg-white/30', i !== idx);
                dot.classList.toggle('w-2', i !== idx);
            });
            current = idx;
        }
 
        if (dots.length > 0) {
            showSlide(0);
            dots.forEach(dot => {
                dot.addEventListener('click', () => showSlide(parseInt(dot.dataset.index)));
            });
            setInterval(() => {
                showSlide((current + 1) % total);
            }, 6000);
        }
    }
 
    // FAQ Toggle Logic
    const faqs = document.querySelectorAll('.faq-item');
    faqs.forEach(faq => {
        faq.addEventListener('click', () => {
            const content = faq.querySelector('.faq-content');
            const svg = faq.querySelector('svg');
            const isOpen = !content.classList.contains('max-h-0');
            
            // Close others if you want
            // faqs.forEach(f => { ... })
 
            if (isOpen) {
                content.classList.add('max-h-0', 'opacity-0');
                svg.classList.remove('rotate-180');
            } else {
                content.classList.remove('max-h-0', 'opacity-0');
                content.style.maxHeight = content.scrollHeight + 'px';
                svg.classList.add('rotate-180');
            }
        });
    });

    // AJAX Category & Filter Switching
    const categoryLinks = document.querySelectorAll('.sf-cat-orb');
    const productGrid = document.getElementById('product-grid');
    const productsSection = document.getElementById('products-section');

    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const targetUrl = url.split('#')[0];

            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

            productGrid.style.opacity = '0.5';
            productGrid.style.pointerEvents = 'none';

            fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                productGrid.innerHTML = html;
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
        
        loadMoreBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            loadMoreBtn.disabled = true;
            loadMoreBtn.querySelector('span').innerText = '{{ $locale == "ar" ? "جاري التحميل..." : "Loading..." }}';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const items = temp.querySelectorAll('.sf-product-card');
                items.forEach(item => productGrid.appendChild(item));

                fetch(url)
                    .then(r => r.text())
                    .then(fullHtml => {
                        const fullDoc = new DOMParser().parseFromString(fullHtml, 'text/html');
                        const nextBtn = fullDoc.getElementById('load-more-btn');
                        if (nextBtn) {
                            loadMoreBtn.dataset.url = nextBtn.dataset.url;
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.querySelector('span').innerText = '{{ $locale == "ar" ? "عرض المزيد" : "Load More" }}';
                        } else {
                            const wrapper = document.getElementById('load-more-wrapper') || loadMoreBtn.parentElement;
                            wrapper.remove();
                        }
                    });
            })
            .catch(() => {
                loadMoreBtn.disabled = false;
                loadMoreBtn.querySelector('span').innerText = '{{ $locale == "ar" ? "عرض المزيد" : "Load More" }}';
            });
        });
    }

    attachLoadMoreEvent();
});
</script>
@endpush
@endsection
