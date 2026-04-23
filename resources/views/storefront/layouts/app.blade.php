<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('meacash-theme');
            document.documentElement.dataset.theme = savedTheme || 'light';
        })();
    </script>
    <title>@yield('title', config('app.name', 'MeaCash'))</title>
    <x-favicon />

    {{-- SEO --}}
    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo-head :seo="$seo ?? null" />
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.meacash.io" crossorigin>
    <link rel="dns-prefetch" href="//cdn.meacash.io">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    {{-- Icons --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0..1&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0..1&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0..1&display=swap" rel="stylesheet"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.isAuthenticated = @json(auth()->check());
        window.isRtl = @json(app()->getLocale() == 'ar');
    </script>
    @stack('styles')
</head>
<body class="bg-background text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col antialiased pb-24 md:pb-0">
    
    <x-noir.header />

    <main class="flex-grow relative overflow-hidden">
        <div class="relative z-10 max-w-[1440px] mx-auto">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 mt-6">
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 animate-fade-in-up">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 mt-6">
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 animate-fade-in-up">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <x-noir.footer />
    <x-noir.mobile-nav />

    {{-- Product Modal (Placeholder for now, will redesign next) --}}
    @include('storefront.partials.product-modal')

    <script src="{{ asset('js/storefront/product-modal.js') }}?v={{ filemtime(public_path('js/storefront/product-modal.js')) }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const handleImage = (img) => {
                if (img.complete) {
                    img.classList.add('sf-img-loaded');
                    img.parentElement?.classList.remove('sf-skeleton');
                } else {
                    img.classList.add('sf-img-loading');
                    img.addEventListener('load', () => {
                        img.classList.add('sf-img-loaded');
                        img.parentElement?.classList.remove('sf-skeleton');
                    }, { once: true });
                    img.addEventListener('error', () => {
                        img.classList.add('sf-img-loaded');
                        img.parentElement?.classList.remove('sf-skeleton');
                    }, { once: true });
                }
            };

            document.querySelectorAll('img').forEach(handleImage);

            // Observer for dynamic content
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) {
                            if (node.tagName === 'IMG') handleImage(node);
                            node.querySelectorAll('img').forEach(handleImage);
                        }
                    });
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
    @stack('scripts')
</body>
</html>
