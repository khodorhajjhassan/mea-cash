@php
    $locale = app()->getLocale();
    $targetLocale = $locale === 'ar' ? 'en' : 'ar';
    $segments = request()->segments();
    $query = request()->except('lang');
    $languageSwitchUrl = request()->fullUrlWithQuery(['lang' => $targetLocale]);

    if (isset($segments[0]) && in_array($segments[0], ['en', 'ar'], true)) {
        $segments[0] = $targetLocale;
        $languageSwitchUrl = url(implode('/', $segments));

        if ($query !== []) {
            $languageSwitchUrl .= '?' . http_build_query($query);
        }
    }

    $isAuth = auth()->check();
    $walletBalance = $isAuth ? (float) (auth()->user()->wallet?->balance ?? 0) : 0;

    $mobileCategories = $mobileCategories ?? collect();

    $categoryIcon = static function ($category): string {
        $name = strtolower((string) $category->name_en);

        return match (true) {
            str_contains($name, 'game') => 'sports_esports',
            str_contains($name, 'gift') => 'card_giftcard',
            str_contains($name, 'stream') => 'movie',
            str_contains($name, 'software') => 'desktop_windows',
            str_contains($name, 'social') => 'forum',
            str_contains($name, 'console') => 'videogame_asset',
            default => $category->icon && mb_strlen($category->icon) > 2 ? $category->icon : 'category',
        };
    };

    $catalogImageUrl = static function (?string $path): string {
        if (! $path) {
            return asset('meacash-logo.png');
        }

        return str_starts_with($path, 'http')
            ? $path
            : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<div id="mobile-nav-backdrop" class="fixed inset-0 z-[90] hidden bg-background/70 backdrop-blur-sm md:hidden" data-mobile-nav-close></div>

<div id="mobile-drawer-categories" class="mobile-drawer" aria-hidden="true">
    <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-outline-variant/40"></div>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-black uppercase text-on-surface">{{ __('Categories') }}</h2>
            <p class="mt-1 font-label text-[10px] uppercase tracking-[0.22em] text-outline">{{ __('Choose a vault') }}</p>
        </div>
        <button type="button" class="mobile-drawer-close" data-mobile-nav-close aria-label="{{ __('Close') }}">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="max-h-[62vh] space-y-4 overflow-y-auto pe-1 no-scrollbar">
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('store.home.locale', ['locale' => $locale]) }}#products-section" class="rounded-2xl border p-4" style="border-color: color-mix(in srgb, var(--mc-gold) 35%, transparent); background-color: color-mix(in srgb, var(--mc-gold) 10%, transparent);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--mc-gold);">apps</span>
                <span class="mt-3 block font-headline text-xs font-black uppercase tracking-widest text-on-surface">{{ __('All Assets') }}</span>
            </a>
            <a href="{{ route('store.home.locale', ['locale' => $locale, 'featured' => 1]) }}#products-section" class="rounded-2xl border border-secondary-container/35 bg-secondary-container/10 p-4">
                <span class="material-symbols-outlined text-3xl text-secondary-container">local_fire_department</span>
                <span class="mt-3 block font-headline text-xs font-black uppercase tracking-widest text-on-surface">{{ __('Hot') }}</span>
            </a>
        </div>

        @foreach($mobileCategories as $category)
            <details class="group rounded-3xl border border-outline-variant/12 bg-surface-container-low/70 p-4" {{ $loop->first ? 'open' : '' }}>
                <summary class="flex cursor-pointer list-none items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-outline-variant/15 bg-surface-container-high transition-all group-open:border-primary-container/30">
                        @if($category->icon && mb_strlen($category->icon) <= 2)
                            <span class="text-2xl">{{ $category->icon }}</span>
                        @else
                            <span class="material-symbols-outlined sf-text-gradient">{{ $categoryIcon($category) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-headline text-sm font-black uppercase text-on-surface">{{ $category->{"name_{$locale}"} ?: $category->name_en }}</h3>
                        <p class="font-label text-[9px] uppercase tracking-widest text-outline">{{ $category->subcategories->count() }} {{ __('vaults') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline transition group-open:rotate-180">expand_more</span>
                </summary>

                <div class="mt-3 grid grid-cols-1 gap-2">
                    <a href="{{ route('store.home.locale', ['locale' => $locale, 'category' => $category->slug]) }}#products-section" class="flex items-center justify-between rounded-2xl border border-primary-container/15 bg-primary-container/10 px-3 py-3 text-start transition hover:border-primary-container/50">
                        <span class="font-label text-[10px] font-black uppercase tracking-widest text-primary-container">{{ __('View all') }}</span>
                        <span class="material-symbols-outlined text-base text-primary-container">arrow_forward</span>
                    </a>

                    @foreach($category->subcategories as $subcategory)
                        <button type="button" class="flex items-center justify-between gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/45 px-3 py-2.5 text-start transition hover:border-primary-container/40 hover:bg-primary-container/10"
                            data-mobile-open-subcategory="{{ $subcategory->slug }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/15 bg-surface-container-low p-1 sf-skeleton">
                                <img src="{{ $catalogImageUrl($subcategory->image) }}" alt="{{ $subcategory->{"name_{$locale}"} ?: $subcategory->name_en }}" 
                                    class="h-full w-full object-contain sf-img-loading" 
                                    loading="lazy" 
                                    onerror="this.src='{{ asset('meacash-logo.png') }}'; this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');"
                                    onload="this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');">
                            </span>
                            <span class="min-w-0 flex-1 truncate font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant">{{ $subcategory->{"name_{$locale}"} ?: $subcategory->name_en }}</span>
                            <span class="material-symbols-outlined text-base text-primary-container">arrow_forward</span>
                        </button>
                    @endforeach
                </div>
            </details>
        @endforeach
    </div>
</div>

<div id="mobile-drawer-search" class="mobile-drawer" aria-hidden="true">
    <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-outline-variant/40"></div>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-black uppercase text-on-surface">{{ __('Search') }}</h2>
            <p class="mt-1 font-label text-[10px] uppercase tracking-[0.22em] text-outline">{{ __('Find products fast') }}</p>
        </div>
        <button type="button" class="mobile-drawer-close" data-mobile-nav-close aria-label="{{ __('Close') }}">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <form action="{{ route('store.search') }}" method="GET" class="mb-4 flex items-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 focus-within:border-primary-container/60">
        <span class="material-symbols-outlined text-outline">search</span>
        <input id="mobile-search-input" name="q" value="{{ request('q') }}" autocomplete="off" class="min-w-0 flex-1 border-0 bg-transparent font-label text-sm uppercase tracking-widest text-on-surface outline-none placeholder:text-outline/50 focus:ring-0" placeholder="{{ __('Search products...') }}">
        <button type="submit" class="rounded-xl bg-primary-container px-3 py-2 font-label text-[9px] font-black uppercase tracking-widest text-on-primary-container">{{ __('Go') }}</button>
    </form>

    <div id="mobile-search-results" class="max-h-[58vh] space-y-2 overflow-y-auto pe-1 no-scrollbar">
        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/45 p-6 text-center font-label text-[10px] uppercase tracking-widest text-outline">
            {{ __('Type at least 2 characters to search') }}
        </div>
    </div>
</div>

<div id="mobile-drawer-profile" class="mobile-drawer" aria-hidden="true">
    <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-outline-variant/40"></div>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-black uppercase text-on-surface">{{ $isAuth ? __('My Profile') : __('Login') }}</h2>
            <p class="mt-1 font-label text-[10px] uppercase tracking-[0.22em] text-outline">{{ __('Select an option below') }}</p>
        </div>
        <button type="button" class="mobile-drawer-close" data-mobile-nav-close aria-label="{{ __('Close') }}">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    @auth
        <div class="mb-4 rounded-3xl border border-outline-variant/15 bg-surface-container-low/70 p-5 text-center">
            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-surface-container-highest text-primary-container">
                <span class="material-symbols-outlined text-3xl">person</span>
            </div>
            <h3 class="font-headline text-xl font-black text-on-surface">{{ auth()->user()->name }}</h3>
            <p class="mt-1 text-xs text-on-surface-variant">{{ auth()->user()->email }}</p>
        </div>

        <a href="{{ route('store.wallet') }}" class="mb-4 block rounded-2xl border border-primary-container/20 bg-primary-container/10 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ __('Wallet') }}</p>
                    <p class="mt-1 font-headline text-2xl font-black text-primary-container">${{ number_format($walletBalance, 2) }}</p>
                </div>
                <span class="material-symbols-outlined text-primary-container">add_card</span>
            </div>
        </a>

        <div class="space-y-3">
            <a href="{{ route('store.dashboard') }}" class="mobile-profile-link">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-headline text-sm font-black uppercase text-on-surface">{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('store.orders') }}" class="mobile-profile-link">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="font-headline text-sm font-black uppercase text-on-surface">{{ __('Orders') }}</span>
            </a>
            <a href="{{ route('store.wallet') }}" class="mobile-profile-link">
                <span class="material-symbols-outlined">account_balance_wallet</span>
                <span class="font-headline text-sm font-black uppercase text-on-surface">{{ __('Wallet') }}</span>
            </a>
            <a href="{{ route('store.profile') }}" class="mobile-profile-link">
                <span class="material-symbols-outlined">manage_accounts</span>
                <span class="font-headline text-sm font-black uppercase text-on-surface">{{ __('User Profile') }}</span>
            </a>
            <a href="{{ $languageSwitchUrl }}" class="mobile-profile-link">
                <span class="material-symbols-outlined">language</span>
                <span class="font-headline text-sm font-black uppercase text-on-surface">{{ $targetLocale === 'ar' ? 'العربية' : 'English' }}</span>
            </a>
        </div>
    @else
        <div class="space-y-3">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-primary-fixed to-secondary-fixed-dim px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.2em] text-on-primary-fixed">
                <span class="material-symbols-outlined">lock_open</span>
                <span>{{ __('Login First') }}</span>
            </a>
            <a href="{{ route('store.register') }}" class="mobile-profile-link"><span class="material-symbols-outlined">person_add</span><span>{{ __('Create Account') }}</span></a>
            <a href="{{ $languageSwitchUrl }}" class="mobile-profile-link"><span class="material-symbols-outlined">language</span><span>{{ $targetLocale === 'ar' ? 'العربية' : 'English' }}</span></a>
        </div>
    @endauth
</div>

<nav class="mc-mobile-nav fixed inset-x-0 bottom-0 z-[95] border-t border-outline-variant/15 px-4 pb-[max(env(safe-area-inset-bottom),0.75rem)] pt-3 backdrop-blur-2xl md:hidden" aria-label="{{ __('Mobile navigation') }}">
    <div class="mx-auto grid max-w-md grid-cols-4 items-end gap-1">
        <a href="{{ route('store.home.locale', ['locale' => $locale]) }}" class="mobile-nav-item {{ request()->routeIs('store.home', 'store.home.locale') ? 'active' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">home</span>
            <span>{{ __('Home') }}</span>
        </a>
        <button type="button" class="mobile-nav-item text-on-surface-variant" data-mobile-drawer-trigger="categories">
            <span class="material-symbols-outlined">grid_view</span>
            <span>{{ __('Category') }}</span>
        </button>
        <button type="button" class="mobile-nav-item mobile-nav-search text-primary-container" data-mobile-drawer-trigger="search">
            <span class="material-symbols-outlined">search</span>
            <span>{{ __('Search') }}</span>
        </button>
        <button type="button" class="mobile-nav-item text-on-surface-variant" data-mobile-drawer-trigger="profile">
            <span class="material-symbols-outlined">{{ $isAuth ? 'account_circle' : 'login' }}</span>
            <span>{{ $isAuth ? __('Profile') : __('Login') }}</span>
        </button>
    </div>
</nav>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const backdrop = document.getElementById('mobile-nav-backdrop');
                const drawers = {
                    categories: document.getElementById('mobile-drawer-categories'),
                    search: document.getElementById('mobile-drawer-search'),
                    profile: document.getElementById('mobile-drawer-profile'),
                };
                const searchInput = document.getElementById('mobile-search-input');
                const searchResults = document.getElementById('mobile-search-results');
                let previousBodyOverflow = '';
                let searchTimer = null;
                let originalActiveItem = null;
                const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[char]));

                const openDrawer = (name) => {
                    const drawer = drawers[name];
                    if (!drawer || !backdrop) return;

                    // Remember original active state
                    if (!originalActiveItem) {
                        originalActiveItem = document.querySelector('.mobile-nav-item.active');
                    }

                    previousBodyOverflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                    backdrop.classList.remove('hidden');
                    Object.values(drawers).forEach((item) => {
                        item?.classList.remove('mobile-drawer-open');
                        item?.setAttribute('aria-hidden', 'true');
                    });
                    drawer.classList.add('mobile-drawer-open');
                    drawer.setAttribute('aria-hidden', 'false');

                    if (name === 'search') {
                        setTimeout(() => searchInput?.focus(), 180);
                    }

                    // Sync bottom nav active state
                    document.querySelectorAll('[data-mobile-drawer-trigger], .mobile-nav-item').forEach(btn => {
                        const isTrigger = btn.dataset.mobileDrawerTrigger === name;
                        btn.classList.toggle('active', isTrigger);
                        btn.classList.toggle('text-on-surface-variant', !isTrigger);
                    });
                };

                const closeDrawers = () => {
                    backdrop?.classList.add('hidden');
                    Object.values(drawers).forEach((item) => {
                        item?.classList.remove('mobile-drawer-open');
                        item?.setAttribute('aria-hidden', 'true');
                    });
                    document.body.style.overflow = previousBodyOverflow;

                    // Restore original active states
                    document.querySelectorAll('[data-mobile-drawer-trigger], .mobile-nav-item').forEach(btn => {
                        const isOriginal = btn === originalActiveItem;
                        btn.classList.toggle('active', isOriginal);
                        btn.classList.toggle('text-on-surface-variant', !isOriginal);
                    });
                    originalActiveItem = null;
                };

                document.querySelectorAll('[data-mobile-drawer-trigger]').forEach((trigger) => {
                    trigger.addEventListener('click', () => openDrawer(trigger.dataset.mobileDrawerTrigger));
                });

                document.querySelectorAll('[data-mobile-nav-close]').forEach((trigger) => {
                    trigger.addEventListener('click', closeDrawers);
                });

                document.querySelectorAll('[data-mobile-open-subcategory]').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        const slug = trigger.dataset.mobileOpenSubcategory;
                        closeDrawers();
                        if (window.openSubcategoryModal && slug) {
                            window.openSubcategoryModal(slug);
                        }
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') closeDrawers();
                });

                const renderSearchMessage = (message) => {
                    if (!searchResults) return;
                    searchResults.innerHTML = `<div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/45 p-6 text-center font-label text-[10px] uppercase tracking-widest text-outline">${message}</div>`;
                };

                const runSearch = async () => {
                    const q = searchInput?.value.trim() || '';
                    if (q.length < 2) {
                        renderSearchMessage(@js(__('Type at least 2 characters to search')));
                        return;
                    }

                    if (!searchResults) return;
                    searchResults.innerHTML = `
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/45 p-6 text-center">
                            <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-primary-container/20 border-t-primary-container"></div>
                            <p class="mt-3 font-label text-[10px] uppercase tracking-widest text-outline">${@js(__('Searching...'))}</p>
                        </div>
                    `;

                    try {
                        const response = await fetch(`{{ route('store.search.api') }}?q=${encodeURIComponent(q)}`, {
                            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const data = await response.json();
                        const results = data.results || [];

                        if (!results.length) {
                            renderSearchMessage(@js(__('No products available')));
                            return;
                        }

                        searchResults.innerHTML = results.map((item) => `
                            <button type="button" class="flex w-full items-center gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-low/70 p-3 text-start transition hover:border-primary-container/45 hover:bg-primary-container/10"
                                data-mobile-search-open="${escapeHtml(item.slug || '')}"
                                data-mobile-search-product="${Number(item.id || item.product_id || 0)}">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/15 bg-surface-container-lowest p-1 sf-skeleton">
                                    <img src="${escapeHtml(item.image || '/meacash-logo.png')}" alt="" class="h-full w-full object-contain sf-img-loading" 
                                        onerror="this.src='/meacash-logo.png'; this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');"
                                        onload="this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');">
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate font-headline text-sm font-black uppercase text-on-surface">${escapeHtml(item.name || '')}</span>
                                    <span class="mt-1 block truncate font-label text-[9px] uppercase tracking-widest text-outline">${escapeHtml(item.category_name || '')}</span>
                                </span>
                                <span class="font-headline text-sm font-black text-primary-container">$${Number(item.price || 0).toFixed(2)}</span>
                            </button>
                        `).join('');

                        searchResults.querySelectorAll('[data-mobile-search-open]').forEach((button) => {
                            button.addEventListener('click', () => {
                                const slug = button.dataset.mobileSearchOpen;
                                const productId = Number(button.dataset.mobileSearchProduct || 0);
                                closeDrawers();
                                if (window.openSubcategoryModal && slug) {
                                    window.openSubcategoryModal(slug, productId || null);
                                }
                            });
                        });
                    } catch (error) {
                        renderSearchMessage(@js(__('Error connecting to vault')));
                    }
                };

                searchInput?.addEventListener('input', () => {
                    window.clearTimeout(searchTimer);
                    searchTimer = window.setTimeout(runSearch, 300);
                });
            });
        </script>
    @endpush
@endonce
