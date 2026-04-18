<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MeaCash')</title>
 
    {{-- SEO --}
    @hasSection('seo')
        @yield('seo')
    @endif
 
    {{-- Fonts: Space Grotesk (English), Cairo (Arabic) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
 
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo'" : "'Space Grotesk'" }}, sans-serif;
            background-color: #030712;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo'" : "'Space Grotesk'" }}, sans-serif;
        }
    </style>
    <script>
        window.isAuthenticated = @json(auth()->check());
        window.isRtl = @json(app()->getLocale() == 'ar');
    </script>
</head>
<body class="sf-body selection:bg-cyan-500/30">
 
{{-- Navbar --}}
<nav class="sf-navbar border-b border-white/5 bg-slate-950/80 backdrop-blur-xl transition-all">
    <div class="sf-container">
        <div class="sf-navbar-inner">
            {{-- Logo --}}
            <a href="{{ route('store.home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-tr from-cyan-400 to-pink-500 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:rotate-12 transition-transform">
                    <span class="font-bold text-xl">M</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">Mea<span class="text-cyan-400">Cash</span></span>
            </a>
 
            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('store.home') }}" class="sf-nav-link text-sm font-medium {{ request()->routeIs('store.home') ? 'active text-cyan-400' : 'text-slate-400' }} hover:text-white transition-colors">
                    {{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}
                </a>
                <a href="{{ route('store.home') }}#products-section" class="sf-nav-link text-sm font-medium text-slate-400 hover:text-white transition-colors">
                    {{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}
                </a>
                <a href="{{ route('store.home') }}#faq-section" class="sf-nav-link text-sm font-medium text-slate-400 hover:text-white transition-colors">
                    {{ app()->getLocale() == 'ar' ? 'الأسئلة الشائعة' : 'FAQ' }}
                </a>
            </div>
 
            {{-- Right side --}}
            <div class="flex items-center gap-4">
                {{-- Search Toggle --}}
                <button id="sf-search-toggle" type="button" class="p-2 text-slate-400 hover:text-white transition-colors" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
 
                {{-- Language --}}
                @php $otherLang = app()->getLocale() == 'ar' ? 'en' : 'ar'; @endphp
                <a href="?lang={{ $otherLang }}" class="hidden sm:flex sf-btn-outline !h-9 px-4 !rounded-full !text-xs !bg-white/5 border-white/10 hover:border-cyan-400/50 hover:text-cyan-400">
                    {{ $otherLang == 'ar' ? 'عربي' : 'EN' }}
                </a>
 
                {{-- Cart --}}
                <a href="{{ route('store.cart') }}" class="relative p-2 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-pink-500 text-[10px] font-bold text-white ring-2 ring-slate-950">{{ $cartCount }}</span>
                    @endif
                </a>
 
                {{-- Auth --}}
                @auth
                    <div class="flex items-center gap-3">
                        <a href="{{ route('store.dashboard') }}" class="sf-btn-cyan !h-9 !px-4 !text-xs">
                            {{ Str::limit(auth()->user()->name, 10) }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-pink-500 transition-colors" title="{{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Logout' }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="sf-btn-cyan !h-9 !px-4 !text-xs">
                        {{ app()->getLocale() == 'ar' ? 'دخول' : 'Login' }}
                    </a>
                @endauth
 
                {{-- Mobile Menu --}}
                <button id="sf-menu-toggle" class="lg:hidden p-2 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
 
        {{-- Search Overlay --}}
        <div id="sf-search-bar" class="hidden pb-4 animate-fade-in">
            <div class="sf-search-wrap relative">
                <form action="{{ route('store.search') }}" method="GET">
                    <input id="sf-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن بطاقاتك المفضلة...' : 'Search for your favorite cards...' }}" 
                           class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 px-12 text-sm text-white focus:border-cyan-400/50 focus:ring-4 focus:ring-cyan-400/10 outline-none transition-all">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-cyan-500 text-slate-950 text-[10px] font-bold px-3 py-1 rounded-lg hover:bg-cyan-400 transition-colors">
                        {{ app()->getLocale() == 'ar' ? 'بحث' : 'SEARCH' }}
                    </button>
                </form>
                <div id="sf-search-results" class="sf-search-results hidden"></div>
            </div>
        </div>
    </div>
 
    {{-- Mobile Menu --}}
    <div id="sf-mobile-menu" class="hidden lg:hidden bg-slate-950 border-t border-white/5">
        <div class="px-6 py-6 space-y-4">
            <a href="{{ route('store.home') }}" class="block text-lg font-medium text-white hover:text-cyan-400 transition-colors">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
            <a href="{{ route('store.home') }}#products-section" class="block text-lg font-medium text-white hover:text-cyan-400 transition-colors">{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</a>
            <a href="{{ route('store.home') }}#faq-section" class="block text-lg font-medium text-white hover:text-cyan-400 transition-colors">{{ app()->getLocale() == 'ar' ? 'الأسئلة الشائعة' : 'FAQ' }}</a>
            <div class="pt-4 border-t border-white/5 flex gap-4">
                <a href="?lang={{ $otherLang }}" class="text-sm text-slate-400 hover:text-white">{{ $otherLang == 'ar' ? 'العربية' : 'English' }}</a>
            </div>
        </div>
    </div>
</nav>
 
{{-- Shell --}}
<main class="sf-shell min-h-screen">
    {{-- Decorative Background Glows --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden z-0">
        <div class="sf-glow sf-glow-primary"></div>
        <div class="sf-glow sf-glow-secondary"></div>
    </div>
 
    <div class="relative z-10 sf-container pb-20">
        {{-- Flash --}}
        @if(session('success'))
            <div class="mt-4 animate-fade-in-up">
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 animate-fade-in-up">
                <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif
 
        @yield('content')
    </div>
</main>
 
{{-- Footer --}}
<footer class="bg-slate-950 border-t border-white/5 py-12 relative overflow-hidden">
    <div class="sf-container relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-12">
            <div class="col-span-2 md:col-span-1">
                <a href="#" class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-gradient-to-tr from-cyan-400 to-pink-500 rounded-lg flex items-center justify-center text-white">
                        <span class="font-bold text-sm">M</span>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">MeaCash</span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed">
                    {{ app()->getLocale() == 'ar' ? 'بوابتك المفضلة لشحن الألعاب والبطاقات الرقمية بأفضل الأسعار وبكل أمان.' : 'Your premier gateway for game top-ups and digital gift cards at the best prices with ultimate security.' }}
                </p>
            </div>
 
            <div>
                <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-widest text-cyan-400">
                    {{ app()->getLocale() == 'ar' ? 'تصفح' : 'Discover' }}
                </h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('store.home') }}" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
                    <li><a href="{{ route('store.home') }}#products-section" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</a></li>
                    <li><a href="{{ route('store.home') }}#faq-section" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'الأسئلة الشائعة' : 'FAQ' }}</a></li>
                </ul>
            </div>
 
            <div>
                <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-widest text-pink-500">
                    {{ app()->getLocale() == 'ar' ? 'الدعم' : 'Support' }}
                </h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'تواصل معنا' : 'Contact Us' }}</a></li>
                    <li><a href="#" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'الشروط والأحكام' : 'Terms of Service' }}</a></li>
                    <li><a href="#" class="text-sm text-slate-400 hover:text-white transition-colors">{{ app()->getLocale() == 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a></li>
                </ul>
            </div>
 
            <div>
                <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-widest text-white">
                    {{ app()->getLocale() == 'ar' ? 'تواصل' : 'Connect' }}
                </h4>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-cyan-500 hover:border-cyan-500 hover:text-slate-950 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>
        </div>
 
        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-slate-500">© {{ date('Y') }} MeaCash. {{ app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
            <div class="flex items-center gap-6">
                <img src="https://sv-bucket-pub.s3.eu-west-3.amazonaws.com/icons/visa.png" alt="Visa" class="h-4 opacity-30 grayscale hover:grayscale-0 transition-all">
                <img src="https://sv-bucket-pub.s3.eu-west-3.amazonaws.com/icons/mastercard.png" alt="Mastercard" class="h-4 opacity-30 grayscale hover:grayscale-0 transition-all">
            </div>
        </div>
    </div>
</footer>
 
{{-- Mobile Bottom Nav --}}
<div class="sm:hidden fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl py-3 px-6 shadow-2xl z-50 flex justify-between items-center">
    <a href="{{ route('store.home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('store.home') ? 'text-cyan-400' : 'text-slate-400' }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    </a>
    <a href="{{ route('store.search') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('store.search') ? 'text-cyan-400' : 'text-slate-400' }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </a>
    <a href="{{ route('store.cart') }}" class="relative flex flex-col items-center gap-1 {{ request()->routeIs('store.cart') ? 'text-cyan-400' : 'text-slate-400' }}">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        @if($cartCount > 0)
            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-pink-500 text-[10px] font-bold text-white ring-2 ring-slate-900">{{ $cartCount }}</span>
        @endif
    </a>
    <a href="{{ auth()->check() ? route('store.dashboard') : route('login') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('store.dashboard') ? 'text-cyan-400' : 'text-slate-400' }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </a>
</div>
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const query = (s) => document.querySelector(s);
 
        // Search Toggle
        query('#sf-search-toggle')?.addEventListener('click', () => {
            const bar = query('#sf-search-bar');
            bar.classList.toggle('hidden');
            if(!bar.classList.contains('hidden')) query('#sf-search-input')?.focus();
        });
 
        // Live Search Results
        let searchTimeout;
        query('#sf-search-input')?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const q = e.target.value.trim();
            const resultsBox = query('#sf-search-results');
            
            if (q.length < 2) {
                resultsBox.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    
                    if (data.results && data.results.length > 0) {
                        resultsBox.innerHTML = data.results.map(item => `
                            <a href="/product/${item.slug}" class="sf-search-result-item" data-product-slug="${item.slug}">
                                <div class="sf-search-thumb">
                                    <img src="${item.image || '/images/placeholder.png'}" alt="">
                                </div>
                                <div class="sf-search-result-copy">
                                    <span class="sf-search-result-title">${item.name}</span>
                                    <span class="sf-search-result-subtitle">${item.category_name}</span>
                                </div>
                                <span class="sf-search-result-price">$${Number(item.price).toFixed(2)}</span>
                            </a>
                        `).join('');
                        resultsBox.classList.remove('hidden');
                    } else {
                        resultsBox.innerHTML = `<div class="sf-search-empty">${window.isRtl ? 'لا توجد نتائج' : 'No results found'}</div>`;
                        resultsBox.classList.remove('hidden');
                    }
                } catch (err) {
                    console.error('Search error:', err);
                }
            }, 300);
        });

        // Sticky observer for category bar
        const categoryBar = query('#sf-category-bar');
        if (categoryBar) {
            const observer = new IntersectionObserver(
                ([e]) => e.target.classList.toggle('is-sticky', e.intersectionRatio < 1),
                { threshold: [1] }
            );
            observer.observe(categoryBar);
        }
    });
</script>
 
{{-- Product Modal --}}
@include('storefront.partials.product-modal')
 
<script src="{{ asset('js/storefront/product-modal.js') }}" defer></script>
@stack('scripts')
</body>
</html>
