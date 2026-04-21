@php
    $locale = app()->getLocale();
    $targetLocale = $locale === 'ar' ? 'en' : 'ar';
    $segments = request()->segments();
    $query = request()->except('lang');

    if (isset($segments[0]) && in_array($segments[0], ['en', 'ar'], true)) {
        $segments[0] = $targetLocale;
    } else {
        array_unshift($segments, $targetLocale);
    }

    $languageSwitchUrl = url(implode('/', $segments));
    if ($query !== []) {
        $languageSwitchUrl .= '?' . http_build_query($query);
    }

    $storeNotifications = $storeNotifications ?? collect();
    $storeUnreadCount = $storeUnreadCount ?? 0;
@endphp

<header
    class="mc-store-header backdrop-blur-xl sticky top-0 z-50 border-b">
    <nav class="flex justify-between items-center w-full max-w-[1440px] mx-auto px-4 md:px-8 h-20">
        <div class="flex items-center gap-4 md:gap-8">
            <a href="{{ route('store.home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('meacash-logo.png') }}" alt="MeaCash"
                    class="h-10 w-auto group-hover:scale-105 transition-transform">
                <span
                    class="mc-gradient-text inline-block px-2 text-xl md:text-2xl font-black italic tracking-tighter">
                    {{ config('app.name', 'MEACASH') }}
                </span>
            </a>

            <div class="hidden md:flex items-center gap-8 font-headline uppercase tracking-widest text-sm">
                <a class="font-bold pb-1 transition-all duration-300 hover:scale-105 {{ request()->routeIs('store.home', 'store.home.locale') ? 'mc-nav-link-active border-b-2' : 'mc-nav-link hover:text-secondary-container' }}"
                    href="{{ route('store.home.locale', ['locale' => $locale]) }}">
                    {{ __('Store') }}
                </a>
                <a class="mc-nav-link transition-all duration-300 hover:scale-105 hover:text-secondary-container"
                    href="{{ route('store.home.locale', ['locale' => $locale, 'featured' => 1]) }}#products-section">
                    {{ __('Hot Deals') }}
                </a>
                <a class="mc-nav-link transition-all duration-300 hover:scale-105 hover:text-secondary-container"
                    href="{{ route('store.contact.locale', ['locale' => $locale]) }}">
                    {{ __('Support') }}
                </a>
            </div>
        </div>

        <div class="flex items-center gap-3 md:gap-6">
            <div class="hidden lg:block relative group/search flex-grow max-w-xl mx-8">
                <form action="{{ route('store.home.locale', ['locale' => app()->getLocale()]) }}" method="GET"
                    class="flex items-center bg-surface-container-highest px-4 py-3 rounded-full border border-outline-variant/30 focus-within:border-primary-container/60 focus-within:bg-surface-container-lowest focus-within:shadow-[0_0_25px_rgba(0,240,255,0.15)] transition-all duration-500 w-full xl:w-80 group-focus-within/search:w-full">
                    <span id="search-icon"
                        class="material-symbols-outlined text-outline text-lg transition-colors group-focus-within/search:text-primary-container">search</span>
                    <span id="search-loader"
                        class="material-symbols-outlined text-primary-container text-lg animate-spin"
                        style="display: none;">refresh</span>
                    <input id="navbar-search"
                        class="bg-transparent border-none focus:ring-0 text-sm font-label tracking-widest flex-grow uppercase text-on-surface placeholder:text-outline/40 ps-3 outline-none"
                        placeholder="{{ __('noir.search_placeholder') ?? 'SEARCH THE VAULT...' }}" type="text" name="q"
                        autocomplete="off" value="{{ request('q') }}">
                    <button type="submit"
                        class="hidden group-focus-within/search:flex items-center gap-1 bg-primary-container text-on-primary-container px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform ml-2">
                        {{ __('Search') }}
                    </button>
                </form>

                {{-- Live Results Dropdown --}}
                <div id="search-results"
                    class="absolute top-full left-0 right-0 mt-2 bg-surface-container-lowest/95 backdrop-blur-md rounded-2xl overflow-hidden hidden z-[60] shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-outline-variant/30">
                    <div id="search-results-list" class="max-h-[400px] overflow-y-auto no-scrollbar">
                        {{-- Results populated via JS --}}
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const input = document.getElementById('navbar-search');
                    const results = document.getElementById('search-results');
                    const list = document.getElementById('search-results-list');
                    const icon = document.getElementById('search-icon');
                    const loader = document.getElementById('search-loader');
                    let timeout = null;

                    input.addEventListener('input', function () {
                        const q = this.value.trim();
                        clearTimeout(timeout);

                        if (q.length < 3) {
                            results.classList.add('hidden');
                            icon.style.display = 'inline-block';
                            loader.style.display = 'none';
                            return;
                        }

                        timeout = setTimeout(() => {
                            // Show results div with a loader inside
                            results.classList.remove('hidden');
                            list.innerHTML = `
                                <div class="p-8 flex flex-col items-center justify-center gap-3">
                                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-primary-container/20 border-t-primary-container"></div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-outline">${@js(__('Searching Vault...'))}</div>
                                </div>
                            `;

                            icon.style.display = 'none';
                            loader.style.display = 'inline-block';

                            fetch(`{{ route('store.search') }}?q=${encodeURIComponent(q)}`, {
                                headers: { 'Accept': 'application/json' }
                            })
                                .then(res => res.json())
                                .then(data => {
                                    icon.style.display = 'inline-block';
                                    loader.style.display = 'none';

                                    if (data.results && data.results.length > 0) {
                                        list.innerHTML = data.results.map(item => `
                                        <div onclick="openSubcategoryModalBySearch('${item.slug}')" class="p-4 border-b border-outline-variant/10 hover:bg-primary-container/10 cursor-pointer flex items-center gap-4 transition-colors">
                                            <div class="w-12 h-12 rounded-xl bg-surface-container-highest overflow-hidden p-1 shrink-0 border border-outline-variant/20">
                                                <img src="${item.image || '/meacash-logo.png'}" class="w-full h-full object-contain">
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="text-[9px] font-black uppercase tracking-widest text-primary-container/70 mb-0.5">${item.category_name}</div>
                                                <div class="text-sm font-headline font-bold text-on-surface truncate">${item.name}</div>
                                            </div>
                                            <div class="text-xs font-headline font-black text-primary-container">$${item.price.toFixed(2)}</div>
                                        </div>
                                    `).join('');
                                    } else {
                                        list.innerHTML = `<div class="p-10 text-center text-[10px] font-black uppercase tracking-[0.3em] text-outline opacity-60">${@js(__('No access codes found'))}</div>`;
                                    }
                                    results.classList.remove('hidden');
                                })
                                .catch(() => {
                                    icon.style.display = 'inline-block';
                                    loader.style.display = 'none';
                                    list.innerHTML = `<div class="p-10 text-center text-[10px] font-black uppercase tracking-widest text-secondary-container">${@js(__('Error connecting to vault'))}</div>`;
                                });
                        }, 500);
                    });

                    // Global helper to link search results to modal
                    window.openSubcategoryModalBySearch = function (slug) {
                        if (window.openSubcategoryModal) {
                            window.openSubcategoryModal(slug);
                            results.classList.add('hidden');
                            input.value = '';
                        } else {
                            window.location.href = `{{ route('store.home') }}?subcategory=${slug}`;
                        }
                    };

                    document.addEventListener('click', (e) => {
                        if (!input.contains(e.target) && !results.contains(e.target)) {
                            results.classList.add('hidden');
                        }
                    });
                });
            </script>

            <div class="flex items-center gap-2 md:gap-4 text-primary-container">
                @auth
                    <div class="relative">
                        <button id="store-notification-bell" type="button"
                            class="mc-icon-button relative flex h-10 w-10 items-center justify-center rounded-full border transition"
                            aria-label="{{ __('Notifications') }}">
                            <span class="material-symbols-outlined text-xl">notifications</span>
                            @if($storeUnreadCount > 0)
                                <span
                                    class="absolute -top-1 -end-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-secondary-container px-1 text-[10px] font-black text-on-secondary-container ring-2 ring-background">
                                    {{ $storeUnreadCount > 9 ? '9+' : $storeUnreadCount }}
                                </span>
                            @endif
                        </button>

                        <div id="store-notification-dropdown"
                            class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-3 hidden w-[calc(100vw-2rem)] max-w-80 overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-lowest/95 shadow-[0_24px_80px_rgba(0,0,0,0.55)] backdrop-blur-xl">
                            <div class="flex items-center justify-between border-b border-outline-variant/10 p-4">
                                <h3 class="font-headline text-sm font-black uppercase text-on-surface">
                                    {{ __('Notifications') }}</h3>
                                @if($storeUnreadCount > 0)
                                    <form method="POST" action="{{ route('store.notifications.read-all') }}">
                                        @csrf
                                        <button type="submit"
                                            class="font-label text-[9px] font-black uppercase tracking-widest text-primary-container">{{ __('Read all') }}</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto no-scrollbar">
                                @forelse($storeNotifications as $notification)
                                    <a href="{{ route('store.notifications.read', $notification->id) }}"
                                        class="flex gap-3 border-b border-outline-variant/8 p-4 transition hover:bg-primary-container/10 {{ $notification->read_at ? 'opacity-60' : '' }}">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-primary-container/15 bg-primary-container/10 text-primary-container">
                                            <span
                                                class="material-symbols-outlined text-lg">{{ $notification->data['icon'] ?? 'notifications' }}</span>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span
                                                class="block truncate font-headline text-xs font-black uppercase text-on-surface">{{ $notification->data['type'] ?? __('Notification') }}</span>
                                            <span
                                                class="mt-1 line-clamp-2 block text-xs leading-relaxed text-on-surface-variant">{{ $notification->data['message'] ?? '' }}</span>
                                            <span
                                                class="mt-1 block font-label text-[9px] uppercase tracking-widest text-outline">{{ $notification->created_at->diffForHumans() }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <div class="p-8 text-center font-label text-[10px] uppercase tracking-widest text-outline">
                                        {{ __('No notifications yet') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endauth

                <button id="theme-toggle" type="button"
                    class="mc-icon-button mc-theme-toggle flex h-10 w-10 items-center justify-center rounded-full border transition"
                    aria-label="{{ __('Toggle theme') }}" aria-pressed="false">
                    <span class="material-symbols-outlined mc-theme-icon mc-theme-icon-sun text-xl" aria-hidden="true">light_mode</span>
                    <span class="material-symbols-outlined mc-theme-icon mc-theme-icon-moon text-xl" aria-hidden="true">dark_mode</span>
                </button>

                {{-- Language Switcher --}}
                <div class="flex items-center ms-1 md:ms-0">
                    <a href="{{ $languageSwitchUrl }}"
                        class="mc-icon-button mc-language-switch flex items-center rounded-full transition-all hover:text-primary-container"
                        aria-label="{{ $targetLocale === 'ar' ? __('Switch to Arabic') : __('Switch to English') }}"
                        title="{{ $targetLocale === 'ar' ? __('Switch to Arabic') : __('Switch to English') }}">
                        <span class="mc-language-flag" aria-hidden="true">{{ $targetLocale === 'ar' ? '🇱🇧' : '🇬🇧' }}</span>
                    </a>
                </div>

                @auth
                    <a href="{{ route('store.dashboard') }}"
                        class="hidden scale-95 active:opacity-80 transition-transform sm:inline-flex">
                        <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden scale-95 active:opacity-80 transition-transform sm:inline-flex">
                        <span class="material-symbols-outlined" data-icon="login">login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
</header>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const bell = document.getElementById('store-notification-bell');
                const dropdown = document.getElementById('store-notification-dropdown');
                const themeToggle = document.getElementById('theme-toggle');

                const syncThemeToggle = () => {
                    const isDark = document.documentElement.dataset.theme === 'dark';
                    themeToggle?.setAttribute('aria-pressed', String(!isDark));
                    themeToggle?.setAttribute('title', isDark ? @js(__('Switch to light mode')) : @js(__('Switch to dark mode')));
                };

                themeToggle?.addEventListener('click', () => {
                    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                    document.documentElement.classList.add('mc-theme-switching');
                    document.documentElement.dataset.theme = nextTheme;
                    localStorage.setItem('meacash-theme', nextTheme);
                    syncThemeToggle();
                    window.requestAnimationFrame(() => {
                        window.requestAnimationFrame(() => {
                            document.documentElement.classList.remove('mc-theme-switching');
                        });
                    });
                });

                syncThemeToggle();

                if (!bell || !dropdown) return;

                bell.addEventListener('click', () => {
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', (event) => {
                    if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        </script>
    @endpush
@endonce
