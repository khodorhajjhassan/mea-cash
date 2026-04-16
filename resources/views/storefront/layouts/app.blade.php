<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MeaCash')</title>

    {{-- SEO --}}
    @hasSection('seo')
        @yield('seo')
    @endif

    {{-- Fonts: Cairo (Arabic), Inter (English), Rajdhani (headings) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo'" : "'Rajdhani'" }}, sans-serif;
        }
    </style>
</head>
<body class="sf-body">

{{-- Navbar --}}
<nav class="sf-navbar">
    <div class="sf-container">
        <div class="sf-navbar-inner">
            {{-- Logo --}}
            <a href="{{ route('store.home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold" style="color: var(--sf-gold);">MeaCash</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-6">
                <a href="{{ route('store.home') }}" class="sf-nav-link {{ request()->routeIs('store.home') ? 'active' : '' }}">
                    {{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}
                </a>
                <a href="{{ route('store.home') }}#products" class="sf-nav-link">
                    {{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}
                </a>
                <a href="{{ route('store.home') }}#how-it-works" class="sf-nav-link">
                    {{ app()->getLocale() == 'ar' ? 'كيف يعمل' : 'How It Works' }}
                </a>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <button id="sf-search-toggle" type="button" class="p-2 transition-colors" style="color: var(--sf-muted);" aria-label="Search" aria-controls="sf-search-bar" aria-expanded="{{ request()->filled('q') ? 'true' : 'false' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                {{-- Language Toggle --}}
                @php $otherLang = app()->getLocale() == 'ar' ? 'en' : 'ar'; @endphp
                <a href="?lang={{ $otherLang }}" class="sf-btn-outline" style="height: 2rem; padding: 0 0.75rem; font-size: 0.75rem; border-radius: 9999px;">
                    {{ $otherLang == 'ar' ? 'عربي' : 'EN' }}
                </a>

                {{-- Cart --}}
                <a href="{{ route('store.cart') }}" class="relative p-2 transition-colors" style="color: var(--sf-muted);">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                    @if($cartCount > 0)
                        <span class="sf-cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                {{-- Auth --}}
                @auth
                    <a href="{{ route('store.dashboard') }}" class="sf-btn-wa" style="height: 2rem; padding: 0 0.75rem; font-size: 0.75rem;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ Str::limit(auth()->user()->name, 10) }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="sf-btn-wa" style="height: 2rem; padding: 0 0.75rem; font-size: 0.75rem;">
                        {{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}
                    </a>
                @endauth

                {{-- Mobile Menu Toggle --}}
                <button id="sf-menu-toggle" class="lg:hidden p-2 transition-colors" style="color: var(--sf-muted);" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Search Bar (hidden by default) --}}
        <div id="sf-search-bar" class="{{ request()->filled('q') ? '' : 'hidden' }} pb-3">
            <div class="sf-search-wrap">
                <form id="sf-search-form" action="{{ route('store.search') }}" method="GET" class="relative">
                    <svg class="absolute {{ app()->getLocale() == 'ar' ? 'right-3' : 'left-3' }} top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--sf-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="sf-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن منتجات...' : 'Search products...' }}" autocomplete="off"
                        class="sf-search-input w-full {{ app()->getLocale() == 'ar' ? 'pr-10 pl-24 sm:pl-28' : 'pl-10 pr-24 sm:pr-28' }} py-2.5 rounded-lg text-sm outline-none transition-all"
                        style="background: var(--sf-surface-alt); border: 1px solid var(--sf-border); color: var(--sf-text);">
                    <button type="submit" class="sf-search-submit">{{ app()->getLocale() == 'ar' ? 'بحث' : 'Search' }}</button>
                </form>
                <div id="sf-search-results" class="sf-search-results hidden" aria-live="polite"></div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="sf-mobile-menu" class="hidden lg:hidden" style="background: var(--sf-bg); border-top: 1px solid var(--sf-border);">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('store.home') }}" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
            <a href="{{ route('store.home') }}#products" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</a>
            <a href="{{ route('store.home') }}#how-it-works" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'كيف يعمل' : 'How It Works' }}</a>
            @auth
                <a href="{{ route('store.dashboard') }}" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</a>
                <a href="{{ route('store.orders') }}" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'طلباتي' : 'My Orders' }}</a>
                <a href="{{ route('store.wallet') }}" class="block py-2 sf-nav-link">{{ app()->getLocale() == 'ar' ? 'محفظتي' : 'My Wallet' }}</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="sf-container mt-4">
        <div class="sf-alert sf-alert-success">{{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="sf-container mt-4">
        <div class="sf-alert sf-alert-error">{{ session('error') }}</div>
    </div>
@endif

{{-- Main Content --}}
<main class="sf-shell">
    {{-- Background Decorations --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="sf-glow sf-glow-primary"></div>
        <div class="sf-glow sf-glow-secondary"></div>
        <div class="sf-grid-pattern"></div>
    </div>

    <div class="relative sf-container" style="padding-bottom: 4rem;">
        @yield('content')
    </div>
</main>

{{-- Footer --}}
<footer class="sf-footer">
    <div class="sf-neon-divider"></div>
    <div class="sf-container py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10 mt-6">
            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1">
                <span class="text-xl font-bold" style="color: var(--sf-gold);">MeaCash</span>
                <p class="mt-2 text-sm" style="color: var(--sf-muted);">
                    {{ app()->getLocale() == 'ar' ? 'منصة موثوقة لشراء البطاقات الرقمية والاشتراكات مع تسليم سريع.' : 'Trusted platform for digital gift cards and subscriptions with fast delivery.' }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-sm font-bold mb-4 uppercase tracking-wider" style="color: var(--sf-text); font-family: 'Rajdhani', sans-serif;">
                    {{ app()->getLocale() == 'ar' ? 'روابط سريعة' : 'Quick Links' }}
                </h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('store.home') }}" class="sf-footer-link">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
                    <li><a href="{{ route('store.home') }}#products" class="sf-footer-link">{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</a></li>
                    <li><a href="{{ route('store.home') }}#how-it-works" class="sf-footer-link">{{ app()->getLocale() == 'ar' ? 'كيف يعمل' : 'How It Works' }}</a></li>
                </ul>
            </div>

            {{-- Account --}}
            <div>
                <h4 class="text-sm font-bold mb-4 uppercase tracking-wider" style="color: var(--sf-text); font-family: 'Rajdhani', sans-serif;">
                    {{ app()->getLocale() == 'ar' ? 'حسابي' : 'Account' }}
                </h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="sf-footer-link">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</a></li>
                    <li><a href="{{ route('store.register') }}" class="sf-footer-link">{{ app()->getLocale() == 'ar' ? 'إنشاء حساب' : 'Register' }}</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-bold mb-4 uppercase tracking-wider" style="color: var(--sf-text); font-family: 'Rajdhani', sans-serif;">
                    {{ app()->getLocale() == 'ar' ? 'تواصل معنا' : 'Contact' }}
                </h4>
                <p class="text-sm" style="color: var(--sf-muted);">📍 {{ app()->getLocale() == 'ar' ? 'لبنان' : 'Lebanon' }}</p>
            </div>
        </div>

        <div style="border-top: 1px solid var(--sf-border);" class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm" style="color: var(--sf-muted);">© {{ date('Y') }} MeaCash. {{ app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
        </div>
    </div>
</footer>

{{-- Mobile Bottom Nav --}}
<div class="sf-bottom-nav sm:hidden">
    <a href="{{ route('store.home') }}" class="sf-bottom-nav-item {{ request()->routeIs('store.home') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        {{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}
    </a>
    <a href="{{ route('store.search') }}" class="sf-bottom-nav-item {{ request()->routeIs('store.search') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        {{ app()->getLocale() == 'ar' ? 'بحث' : 'Search' }}
    </a>
    <a href="{{ route('store.cart') }}" class="sf-bottom-nav-item {{ request()->routeIs('store.cart') ? 'active' : '' }} relative">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        {{ app()->getLocale() == 'ar' ? 'السلة' : 'Cart' }}
        @if($cartCount > 0)
            <span class="sf-cart-badge" style="top: -2px; right: 8px;">{{ $cartCount }}</span>
        @endif
    </a>
    <a href="{{ auth()->check() ? route('store.dashboard') : route('login') }}" class="sf-bottom-nav-item {{ request()->routeIs('store.dashboard') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        {{ app()->getLocale() == 'ar' ? 'حسابي' : 'Account' }}
    </a>
</div>

<script>
    (() => {
        const searchToggle = document.getElementById('sf-search-toggle');
        const searchBar = document.getElementById('sf-search-bar');
        const searchForm = document.getElementById('sf-search-form');
        const searchInput = document.getElementById('sf-search-input');
        const searchResults = document.getElementById('sf-search-results');
        const searchEndpoint = @json(route('store.search'));
        const isArabic = document.documentElement.lang.startsWith('ar');

        let debounceTimer = null;
        let requestCounter = 0;

        const escapeHtml = (value) => {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        };

        const renderSearchResults = (items) => {
            if (!searchResults) return;

            if (!Array.isArray(items) || items.length === 0) {
                searchResults.innerHTML = `<div class="sf-search-empty">${isArabic ? 'لا توجد نتائج مطابقة' : 'No matching results found'}</div>`;
                searchResults.classList.remove('hidden');
                return;
            }

            searchResults.innerHTML = items.map((item) => `
                <button type="button" class="sf-search-result-item sf-search-result-button" data-product-slug="${escapeHtml(item.slug)}">
                    <span class="sf-search-thumb">
                        ${item.image
                            ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">`
                            : `<span class="sf-search-thumb-placeholder">🎮</span>`
                        }
                    </span>
                    <span class="sf-search-result-copy">
                        <span class="sf-search-result-title">${escapeHtml(item.name)}</span>
                        <span class="sf-search-result-subtitle">${escapeHtml(item.category ?? item.subcategory ?? '')}</span>
                    </span>
                    <span class="sf-search-result-price">$${Number(item.price ?? 0).toFixed(2)}</span>
                </button>
            `).join('');
            searchResults.classList.remove('hidden');
        };

        const hideResults = () => {
            if (!searchResults) return;
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
        };

        const fetchLiveResults = async (query) => {
            const requestId = ++requestCounter;
            try {
                const response = await fetch(`${searchEndpoint}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Search request failed');
                }

                const items = await response.json();
                if (requestId !== requestCounter) return;
                renderSearchResults(items);
            } catch (error) {
                if (requestId !== requestCounter) return;
                hideResults();
            }
        };

        const openSearch = () => {
            if (!searchBar) return;
            searchBar.classList.remove('hidden');
            searchToggle?.setAttribute('aria-expanded', 'true');
            searchInput?.focus();
        };

        searchToggle?.addEventListener('click', () => {
            if (!searchBar) return;

            if (searchBar.classList.contains('hidden')) {
                openSearch();
                return;
            }

            const query = searchInput?.value.trim() ?? '';
            if (query.length > 0) {
                searchForm?.requestSubmit();
                return;
            }

            searchInput?.focus();
        });

        searchForm?.addEventListener('submit', (event) => {
            const query = searchInput?.value.trim() ?? '';
            if (query === '') {
                event.preventDefault();
                searchInput?.focus();
                hideResults();
            }
        });

        searchInput?.addEventListener('input', () => {
            const query = searchInput.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                hideResults();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchLiveResults(query);
            }, 220);
        });

        searchInput?.addEventListener('focus', () => {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                fetchLiveResults(query);
            }
        });

        searchResults?.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target.closest('[data-product-slug]') : null;
            if (target) {
                setTimeout(() => hideResults(), 0);
            }
        });

        document.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target : null;
            const insideSearch = target?.closest('#sf-search-bar') || target?.closest('#sf-search-toggle');
            if (!insideSearch) {
                hideResults();
            }
        });

        // Mobile menu toggle
        document.getElementById('sf-menu-toggle')?.addEventListener('click', () => {
            document.getElementById('sf-mobile-menu')?.classList.toggle('hidden');
        });

        // Auto-dismiss alerts
        document.querySelectorAll('.sf-alert').forEach(el => {
            setTimeout(() => { el.style.transition = 'opacity 0.5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }, 4000);
        });
    })();
</script>

{{-- Product Modal --}}
@include('storefront.partials.product-modal')

<script src="{{ asset('js/storefront/product-modal.js') }}" defer></script>
@stack('scripts')
</body>
</html>
