@php
    $locale = app()->getLocale();
    $homeUrl = route('store.home');
    $contactUrl = route('store.contact');
    $aboutUrl = route('store.page', ['slug' => 'about']);
    $privacyUrl = route('store.page', ['slug' => 'privacy-policy']);
    $termsUrl = route('store.page', ['slug' => 'terms-and-conditions']);
    $refundUrl = route('store.page', ['slug' => 'refund-terms']);
@endphp

<footer class="relative overflow-hidden border-t border-outline-variant/20 bg-surface-container-lowest">
    <button
        class="fixed bottom-24 end-5 z-50 hidden h-12 w-12 items-center justify-center rounded-full bg-primary-container text-on-primary-container shadow-[0_0_20px_rgba(0,240,255,0.4)] transition-transform hover:scale-110 md:flex md:bottom-8 md:end-8"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <span class="material-symbols-outlined font-bold">arrow_upward</span>
    </button>

    <div class="px-4 py-8 md:hidden">
            <div
            class="mx-auto max-w-md rounded-[1.75rem] border border-outline-variant/10 bg-surface-container-low/55 p-5 text-center">
            <div class="flex items-center justify-center gap-3">
                <x-noir.logo alt="MeaCash" class="h-8 w-8" sizes="32px" />
                <span
                    class="font-headline text-2xl font-black italic tracking-tighter text-primary-container">MEACASH</span>
            </div>

            <nav class="mt-5 flex flex-wrap items-center justify-center gap-x-4 gap-y-3 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant"
                aria-label="{{ __('Footer links') }}">
                <a class="transition hover:text-primary-container" href="{{ $aboutUrl }}">{{ __('About') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $contactUrl }}">{{ __('Contact Us') }}</a>
                <a class="transition hover:text-primary-container"
                    href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $termsUrl }}">{{ __('Terms') }}</a>
                <a class="transition hover:text-primary-container" href="{{ $refundUrl }}">{{ __('Refunds') }}</a>
            </nav>

            <div class="mt-5 flex items-center justify-center gap-2">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                <span
                    class="font-label text-[9px] font-black uppercase tracking-widest text-on-surface-variant">{{ __('noir.systems_operational') }}</span>
            </div>

            <a href="https://broadstark.com" target="_blank" rel="noopener noreferrer"
                class="mt-4 inline-flex items-center justify-center gap-2 font-label text-[9px] font-black uppercase tracking-widest text-outline transition hover:text-primary-container">
                <span class="material-symbols-outlined text-sm">code</span>
                <span>{{ $locale === 'ar' ? 'تم البناء بواسطة Broadstark' : 'Website built by Broadstark' }}</span>
            </a>

            <p class="mt-4 font-label text-[9px] font-black uppercase tracking-[0.2em] text-outline/75">
                &copy; {{ date('Y') }} MEACASH
            </p>
        </div>
    </div>

    <div class="hidden pb-10 pt-20 md:block">
        <div class="mx-auto grid max-w-[1440px] grid-cols-1 gap-10 px-8 md:grid-cols-2 lg:grid-cols-5 lg:gap-8">
            <div class="lg:col-span-2">
                <div class="mb-6 flex items-center gap-3">
                    <x-noir.logo alt="MeaCash" class="h-10 w-10" sizes="40px" />
                    <span
                        class="inline-block text-xl md:text-3xl font-black italic tracking-tighter text-transparent bg-clip-text px-2 mc-gradient-text">{{ config('app.name', 'MEACASH') }}</span>
                </div>
                <p class="mb-8 max-w-sm font-body text-sm leading-relaxed text-on-surface-variant">
                    {{ __('noir.footer_tagline') }}
                </p>
            </div>

            <div>
                <h4
                    class="mb-6 inline-block border-b border-primary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.platform') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $homeUrl }}">{{ __('Store') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $homeUrl }}#products-section">{{ __('Products') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $aboutUrl }}">{{ __('About') }}</a></li>
                </ul>
            </div>

            <div>
                <h4
                    class="mb-6 inline-block border-b border-secondary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.support') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    @auth
                        <li><a class="transition-colors hover:text-secondary-container"
                                href="{{ route('store.wallet') }}">{{ __('Wallet') }}</a></li>
                        <li><a class="transition-colors hover:text-secondary-container"
                                href="{{ route('store.orders') }}">{{ __('Orders') }}</a></li>
                    @else
                        <li><a class="transition-colors hover:text-secondary-container"
                                href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li><a class="transition-colors hover:text-secondary-container"
                                href="{{ route('store.register') }}">{{ __('Create Account') }}</a></li>
                    @endauth
                    <li><a class="transition-colors hover:text-secondary-container"
                            href="{{ $contactUrl }}">{{ __('Contact Us') }}</a></li>
                    <li><a class="transition-colors hover:text-secondary-container"
                            href="{{ $refundUrl }}">{{ __('Terms of Refunds') }}</a></li>
                </ul>
            </div>

            <div>
                <h4
                    class="mb-6 inline-block border-b border-primary-container/20 pb-2 font-headline text-sm font-bold uppercase tracking-widest text-on-surface">
                    {{ __('noir.legal') }}
                </h4>
                <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $termsUrl }}">{{ __('Terms and Conditions') }}</a></li>
                    <li><a class="transition-colors hover:text-primary-container"
                            href="{{ $refundUrl }}">{{ __('Refund Terms') }}</a></li>
                </ul>
            </div>
        </div>

        <div
            class="mx-auto mt-16 flex max-w-[1440px] flex-col items-center justify-between gap-6 border-t border-outline-variant/10 px-8 pt-8 md:mt-20 md:flex-row">
            <p
                class="text-center font-headline text-[10px] uppercase tracking-[0.24em] text-on-surface-variant md:text-start md:tracking-[0.3em]">
                &copy; {{ date('Y') }} MEACASH. HIGH-FIDELITY DIGITAL ASSETS. ALL RIGHTS RESERVED.
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
                        {{ $locale === 'ar' ? 'تم البناء بواسطة Broadstark' : 'Website built by Broadstark' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
