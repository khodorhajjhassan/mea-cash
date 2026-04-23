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

    $siteSettings = $siteSettings ?? [];

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
            return asset('meacash-logo-64.webp');
        }

        return str_starts_with($path, 'http')
            ? $path
            : \Illuminate\Support\Facades\Storage::url($path);
    };
@endphp

<div id="mobile-nav-backdrop" class="fixed inset-0 z-[90] hidden bg-background/70 backdrop-blur-sm md:hidden" data-mobile-nav-close></div>

<div id="mobile-drawer-categories" class="mobile-drawer" aria-hidden="true" inert>
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
            <a href="{{ route('store.home') }}#products-section" class="rounded-2xl border p-4" style="border-color: color-mix(in srgb, var(--mc-gold) 35%, transparent); background-color: color-mix(in srgb, var(--mc-gold) 10%, transparent);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--mc-gold);">apps</span>
                <span class="mt-3 block font-headline text-xs font-black uppercase tracking-widest text-on-surface">{{ __('All Assets') }}</span>
            </a>
            <a href="{{ route('store.home', ['featured' => 1]) }}#products-section" class="rounded-2xl border border-secondary-container/35 bg-secondary-container/10 p-4">
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
                    <a href="{{ route('store.home', ['category' => $category->slug]) }}#products-section" class="flex items-center justify-between rounded-2xl border border-primary-container/15 bg-primary-container/10 px-3 py-3 text-start transition hover:border-primary-container/50">
                        <span class="font-label text-[10px] font-black uppercase tracking-widest text-primary-container">{{ __('View all') }}</span>
                        <span class="material-symbols-outlined text-base text-primary-container">arrow_forward</span>
                    </a>

                    @foreach($category->subcategories as $subcategory)
                        <button type="button" class="flex items-center justify-between gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/45 px-3 py-2.5 text-start transition hover:border-primary-container/40 hover:bg-primary-container/10"
                            data-mobile-open-subcategory="{{ $subcategory->slug }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/15 bg-surface-container-low p-1 sf-skeleton">
                                <img src="{{ $catalogImageUrl($subcategory->image) }}" alt="{{ $subcategory->{"name_{$locale}"} ?: $subcategory->name_en }}" 
                                    width="40" height="40"
                                    class="h-full w-full object-contain sf-img-loading" 
                                    loading="lazy" 
                                    onerror="this.onerror=null; this.src='{{ asset('meacash-logo-128.png') }}'; this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');"
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

<div id="mobile-drawer-search" class="mobile-drawer" aria-hidden="true" inert>
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

<div id="mobile-drawer-profile" class="mobile-drawer" aria-hidden="true" inert>
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
        </div>
    @else
        <div class="space-y-3">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.2em] text-on-primary-container">
                <span class="material-symbols-outlined">lock_open</span>
                <span>{{ __('Login First') }}</span>
            </a>
            <a href="{{ route('store.register') }}" class="mobile-profile-link"><span class="material-symbols-outlined">person_add</span><span>{{ __('Create Account') }}</span></a>
        </div>
    @endauth
</div>

<div id="mobile-drawer-settings" class="mobile-drawer" aria-hidden="true" inert>
    <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-outline-variant/40"></div>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-black uppercase text-on-surface">{{ __('Settings') }}</h2>
            <p class="mt-1 font-label text-[10px] uppercase tracking-[0.22em] text-outline">{{ __('Preferences & links') }}</p>
        </div>
        <button type="button" class="mobile-drawer-close" data-mobile-nav-close aria-label="{{ __('Close') }}">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    {{-- Logo & Site Name --}}
    <div class="mb-5 flex items-center justify-center gap-3 rounded-3xl border border-outline-variant/15 bg-surface-container-low/70 p-5">
        <x-noir.logo alt="MeaCash" class="h-10 w-10" sizes="40px" />
        <span class="font-headline text-2xl font-black italic tracking-tighter mc-brand-wordmark">{{ $siteSettings['site_name'] ?? 'MeaCash' }}</span>
    </div>

    <div class="max-h-[55vh] space-y-3 overflow-y-auto pe-1 no-scrollbar">
        {{-- Language --}}
        <a href="{{ $languageSwitchUrl }}" class="mobile-profile-link">
            <span class="mc-language-flag flex h-6 w-6 items-center justify-center overflow-hidden rounded-full border border-outline-variant/20">
                <img src="{{ $targetLocale === 'ar' ? 'https://flagcdn.com/lb.svg' : 'https://flagcdn.com/gb.svg' }}" alt="" width="24" height="24" class="h-full w-full object-cover">
            </span>
            <span class="flex-1 font-headline text-sm font-black uppercase text-on-surface">{{ $targetLocale === 'ar' ? 'العربية' : 'English' }}</span>
            <span class="material-symbols-outlined text-base text-primary-container">arrow_forward</span>
        </a>

        {{-- Theme --}}
        <button type="button" class="mc-theme-toggle mobile-profile-link w-full">
            <span class="material-symbols-outlined mc-theme-icon mc-theme-icon-sun">light_mode</span>
            <span class="material-symbols-outlined mc-theme-icon mc-theme-icon-moon">dark_mode</span>
            <span class="flex-1 font-headline text-sm font-black uppercase text-on-surface">{{ __('Toggle Theme') }}</span>
        </button>

        {{-- Social Media --}}
        @php
            $socials = [
                ['key' => 'social_facebook',  'label' => 'Facebook',  'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
                ['key' => 'social_instagram', 'label' => 'Instagram', 'icon' => '<path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 100 12.324 6.162 6.162 0 100-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405a1.441 1.441 0 11-2.882 0 1.441 1.441 0 012.882 0z"/>'],
                ['key' => 'social_twitter',   'label' => 'X',         'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
                ['key' => 'social_tiktok',    'label' => 'TikTok',    'icon' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>'],
                ['key' => 'social_whatsapp',  'label' => 'WhatsApp',  'icon' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>'],
            ];
        @endphp

        <div class="grid grid-cols-5 gap-3">
            @foreach($socials as $social)
                @if(!empty($siteSettings[$social['key']] ?? ''))
                    <a href="{{ $siteSettings[$social['key']] }}" target="_blank" rel="noopener noreferrer"
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-outline-variant/15 bg-surface-container-high/50 text-on-surface-variant transition-all hover:border-primary-container/50 hover:text-primary-container hover:shadow-[0_0_12px_rgba(0,240,255,0.2)]"
                        title="{{ $social['label'] }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">{!! $social['icon'] !!}</svg>
                    </a>
                @endif
            @endforeach
        </div>

        {{-- Support Email --}}
        @if(!empty($siteSettings['site_email'] ?? ''))
            <a href="mailto:{{ $siteSettings['site_email'] }}" class="mobile-profile-link">
                <span class="material-symbols-outlined text-primary-container">mail</span>
                <span class="flex-1 min-w-0">
                    <span class="block font-headline text-sm font-black uppercase text-on-surface">{{ __('Support Email') }}</span>
                    <span class="block truncate font-label text-[10px] tracking-widest text-outline">{{ $siteSettings['site_email'] }}</span>
                </span>
            </a>
        @endif

        {{-- WhatsApp Help --}}
        @if(!empty($siteSettings['site_phone'] ?? ''))
            @php $whatsappNumber = preg_replace('/[^0-9]/', '', $siteSettings['site_phone']); @endphp
            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer" class="mobile-profile-link">
                <svg class="h-5 w-5 shrink-0 text-emerald-500" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <span class="flex-1 min-w-0">
                    <span class="block font-headline text-sm font-black uppercase text-on-surface">{{ __('WhatsApp Help') }}</span>
                    <span class="block truncate font-label text-[10px] tracking-widest text-outline">{{ $siteSettings['site_phone'] }}</span>
                </span>
                <span class="material-symbols-outlined text-base text-outline">open_in_new</span>
            </a>
        @endif
    </div>
</div>

{{-- Fixed Settings Button (top-right, mobile only) --}}
<button type="button"
    class="fixed top-4 end-4 z-[96] flex h-9 w-9 items-center justify-center rounded-full border border-outline-variant/20 bg-surface-container-lowest/90 text-on-surface-variant shadow-lg backdrop-blur-xl transition-all hover:border-primary-container/50 hover:text-primary-container md:hidden"
    data-mobile-drawer-trigger="settings"
    aria-label="{{ __('Settings') }}">
    <span class="material-symbols-outlined text-lg">settings</span>
</button>

<nav class="mc-mobile-nav fixed inset-x-0 bottom-0 z-[95] border-t border-outline-variant/15 px-4 pb-[max(env(safe-area-inset-bottom),0.75rem)] pt-3 backdrop-blur-2xl md:hidden" aria-label="{{ __('Mobile navigation') }}">
    <div class="mx-auto grid max-w-md grid-cols-4 items-end gap-1">
        <a href="{{ route('store.home') }}" class="mobile-nav-item {{ request()->routeIs('store.home') ? 'active' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined">home</span>
            <span>{{ __('Home') }}</span>
        </a>
        <button type="button" class="mobile-nav-item text-on-surface-variant" data-mobile-drawer-trigger="categories">
            <span class="material-symbols-outlined">grid_view</span>
            <span>{{ __('Category') }}</span>
        </button>
        <button type="button" class="mobile-nav-item text-on-surface-variant" data-mobile-drawer-trigger="search">
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
                    settings: document.getElementById('mobile-drawer-settings'),
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
                    drawer.removeAttribute('inert');

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
                        item?.setAttribute('inert', '');
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
                                    <img src="${escapeHtml(item.image || '/meacash-logo-64.webp')}" alt="" width="56" height="56" class="h-full w-full object-contain sf-img-loading"
                                        onerror="this.onerror=null; this.src='/meacash-logo-128.png'; this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');"
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
