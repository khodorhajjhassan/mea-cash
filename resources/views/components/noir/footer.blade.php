@php
    $locale = app()->getLocale();
    $homeUrl = route('store.home', ['locale' => $locale]);
    $contactUrl = route('store.contact', ['locale' => $locale]);
    $aboutUrl = route('store.page', ['locale' => $locale, 'slug' => 'about']);
    $privacyUrl = route('store.page', ['locale' => $locale, 'slug' => 'privacy-policy']);
    $termsUrl = route('store.page', ['locale' => $locale, 'slug' => 'terms-and-conditions']);
    $refundUrl = route('store.page', ['locale' => $locale, 'slug' => 'refund-terms']);

    $siteSettings = $siteSettings ?? [];

    $footerSocials = [
        ['key' => 'social_facebook',  'label' => 'Facebook',  'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
        ['key' => 'social_instagram', 'label' => 'Instagram', 'icon' => '<path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913a6.006 6.006 0 001.384 2.126 5.92 5.92 0 002.126 1.384c.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558a6.006 6.006 0 002.126-1.384 5.92 5.92 0 001.384-2.126c.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913a6.006 6.006 0 00-1.384-2.126A5.92 5.92 0 0019.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227a3.81 3.81 0 01-.899 1.382 3.744 3.744 0 01-1.38.896c-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07s-3.586-.015-4.859-.074c-1.171-.061-1.816-.256-2.236-.421a3.716 3.716 0 01-1.379-.899 3.644 3.644 0 01-.9-1.38c-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 100 12.324 6.162 6.162 0 100-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405a1.441 1.441 0 11-2.882 0 1.441 1.441 0 012.882 0z"/>'],
        ['key' => 'social_twitter',   'label' => 'X',         'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
        ['key' => 'social_tiktok',    'label' => 'TikTok',    'icon' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>'],
        ['key' => 'social_whatsapp',  'label' => 'WhatsApp',  'icon' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>'],
    ];
@endphp

<footer class="relative overflow-hidden border-t border-outline-variant/20 bg-surface-container-lowest">
    <button
        class="fixed bottom-24 end-5 z-50 hidden h-12 w-12 items-center justify-center rounded-full bg-primary-container text-on-primary-container shadow-[0_0_20px_rgba(0,240,255,0.4)] transition-transform hover:scale-110 md:flex md:bottom-8 md:end-8"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <span class="material-symbols-outlined font-bold">arrow_upward</span>
    </button>

    {{-- Mobile Footer --}}
    <div class="px-4 py-8 md:hidden">
        <div class="mx-auto max-w-md rounded-[1.75rem] border border-outline-variant/10 bg-surface-container-low/55 p-5 text-center">
            <div class="flex items-center justify-center gap-3">
                <x-noir.logo alt="MeaCash" class="h-8 w-8" sizes="32px" />
                <span class="font-headline text-2xl font-black italic tracking-tighter mc-brand-wordmark">MeaCash</span>
            </div>

            <nav class="mt-5 flex flex-wrap items-center justify-center gap-x-4 gap-y-3 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant"
                aria-label="{{ __('Footer links') }}">
                <a class="transition hover:text-primary-container" href="{{ $aboutUrl }}">{{ __('About') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $contactUrl }}">{{ __('Contact Us') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $termsUrl }}">{{ __('Terms') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $refundUrl }}">{{ __('Refunds') }}</a>
            </nav>

            <div class="mt-5 flex items-center justify-center gap-2">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                <span class="font-label text-[9px] font-black uppercase tracking-widest text-on-surface-variant">{{ __('noir.systems_operational') }}</span>
            </div>

            <a href="https://broadstark.com" target="_blank" rel="noopener noreferrer"
                class="mt-4 inline-flex items-center justify-center gap-2 font-label text-[9px] font-black uppercase tracking-widest text-outline transition hover:text-primary-container">
                <span class="material-symbols-outlined text-sm">code</span>
                <span>{!! $locale === 'ar' ? 'تم البناء بواسطة <span class="mc-broadstark-gradient">Broadstark</span>' : 'Website built by <span class="mc-broadstark-gradient">Broadstark</span>' !!}</span>
            </a>

            <p class="mt-4 font-label text-[9px] font-black uppercase tracking-[0.2em] text-outline/75">
                &copy; {{ date('Y') }} MeaCash
            </p>
        </div>
    </div>

    {{-- Desktop Footer --}}
    <div class="hidden pb-10 pt-20 md:block">
        <div class="mx-auto grid max-w-[1440px] grid-cols-1 gap-10 px-8 md:grid-cols-2 lg:grid-cols-5 lg:gap-8">
            <div class="lg:col-span-2">
                <div class="mb-6 flex items-center gap-3">
                    <x-noir.logo alt="MeaCash" class="h-10 w-10" sizes="40px" />
                    <span class="inline-block text-xl md:text-3xl font-black italic tracking-tighter px-2 mc-gradient-text mc-brand-wordmark">MeaCash</span>
                </div>
                <p class="mb-8 max-w-sm font-body text-sm leading-relaxed text-on-surface-variant">
                    {{ __('noir.footer_tagline') }}
                </p>

                {{-- Social Media Icons --}}
                <div class="flex items-center gap-3">
                    @foreach($footerSocials as $fs)
                        @if(!empty($siteSettings[$fs['key']] ?? ''))
                            <a href="{{ $siteSettings[$fs['key']] }}" target="_blank" rel="noopener noreferrer"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/20 bg-surface-container-high/50 text-on-surface-variant transition-all hover:border-primary-container/50 hover:text-primary-container hover:shadow-[0_0_12px_rgba(0,240,255,0.2)]"
                                title="{{ $fs['label'] }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">{!! $fs['icon'] !!}</svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="mb-6 inline-block border-b border-primary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.platform') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $homeUrl }}">{{ __('Store') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $homeUrl }}#products-section">{{ __('Products') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $aboutUrl }}">{{ __('About') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 inline-block border-b border-secondary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.support') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    @auth
                        <li><a class="transition-colors hover:text-secondary-container" href="{{ route('store.wallet', ['locale' => $locale]) }}">{{ __('Wallet') }}</a></li>
                        <li><a class="transition-colors hover:text-secondary-container" href="{{ route('store.orders', ['locale' => $locale]) }}">{{ __('Orders') }}</a></li>
                    @else
                        <li><a class="transition-colors hover:text-secondary-container" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li><a class="transition-colors hover:text-secondary-container" href="{{ route('store.register', ['locale' => $locale]) }}">{{ __('Create Account') }}</a></li>
                    @endauth
                    <li><a class="transition-colors hover:text-secondary-container" href="{{ $contactUrl }}">{{ __('Contact Us') }}</a></li>
                    <li><a class="transition-colors hover:text-secondary-container" href="{{ $refundUrl }}">{{ __('Terms of Refunds') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 inline-block border-b border-primary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.legal') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $termsUrl }}">{{ __('Terms and Conditions') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container" href="{{ $refundUrl }}">{{ __('Refund Terms') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mx-auto mt-16 flex max-w-[1440px] flex-col items-center justify-between gap-6 border-t border-outline-variant/10 px-8 pt-8 md:mt-20 md:flex-row">
            <p class="text-center font-headline text-[10px] uppercase tracking-[0.24em] text-on-surface-variant md:text-start md:tracking-[0.3em]">
                &copy; {{ date('Y') }} MeaCash. HIGH-FIDELITY DIGITAL ASSETS. ALL RIGHTS RESERVED.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row sm:gap-8">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                    <span class="font-headline text-[10px] uppercase tracking-widest text-on-surface-variant">
                        {{ __('noir.systems_operational') }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-on-surface-variant">code</span>
                    <a href="https://broadstark.com" target="_blank" rel="noopener noreferrer"
                        class="font-headline text-[10px] uppercase tracking-widest text-on-surface-variant transition hover:text-primary-container">
                        {!! $locale === 'ar' ? 'تم البناء بواسطة <span class="mc-broadstark-gradient">Broadstark</span>' : 'Website built by <span class="mc-broadstark-gradient">Broadstark</span>' !!}
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
