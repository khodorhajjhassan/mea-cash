<footer class="bg-surface-container-lowest border-t border-outline-variant/20 pt-20 pb-10 relative overflow-hidden">
    {{-- Floating Back to Top FAB --}}
    <button class="fixed bottom-24 end-5 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-primary-container text-on-primary-container shadow-[0_0_20px_rgba(0,240,255,0.4)] transition-transform hover:scale-110 md:bottom-8 md:end-8" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <span class="material-symbols-outlined font-bold">arrow_upward</span>
    </button>

    <div class="max-w-[1440px] mx-auto grid grid-cols-1 gap-10 px-4 md:grid-cols-2 md:px-8 lg:grid-cols-5 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3 mb-6">
                <img src="{{ asset('meacash-logo.png') }}" alt="MeaCash" class="h-10 w-auto">
                <span class="text-[#00f0ff] font-headline font-black text-3xl tracking-tighter italic">MEACASH</span>
            </div>
            <p class="text-on-surface-variant text-sm leading-relaxed max-w-sm mb-8 font-body">
                {{ __('noir.footer_tagline') }}
            </p>
            <div class="flex gap-4">
                <x-noir.social-link icon="share" color="primary" href="#" />
                <x-noir.social-link icon="forum" color="secondary" href="#" />
                <x-noir.social-link icon="public" color="primary" href="#" />
            </div>
        </div>

        <div>
            <h4 class="font-headline font-bold text-sm tracking-widest uppercase mb-6 text-on-surface border-b border-primary-container/20 pb-2 inline-block">
                {{ __('noir.platform') }}
            </h4>
            <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Store') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('How it works') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Affiliate Program') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Drops & Events') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-headline font-bold text-sm tracking-widest uppercase mb-6 text-on-surface border-b border-secondary-container/20 pb-2 inline-block">
                {{ __('noir.support') }}
            </h4>
            <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                <li><a class="hover:text-secondary-container transition-colors" href="#">{{ __('Help Center') }}</a></li>
                <li><a class="hover:text-secondary-container transition-colors" href="#">{{ __('Refund Policy') }}</a></li>
                <li><a class="hover:text-secondary-container transition-colors" href="#">{{ __('Discord Server') }}</a></li>
                <li><a class="hover:text-secondary-container transition-colors" href="#">{{ __('Contact Agent') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-headline font-bold text-sm tracking-widest uppercase mb-6 text-on-surface border-b border-primary-container/20 pb-2 inline-block">
                {{ __('noir.legal') }}
            </h4>
            <ul class="space-y-4 font-label text-xs uppercase tracking-widest text-on-surface-variant">
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Privacy Protocol') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Terms of Service') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('Partner License') }}</a></li>
                <li><a class="hover:text-primary-container transition-colors" href="#">{{ __('API Governance') }}</a></li>
            </ul>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto mt-16 flex flex-col items-center justify-between gap-6 border-t border-outline-variant/10 px-4 pt-8 md:mt-20 md:flex-row md:px-8">
        <p class="text-center font-headline text-[10px] uppercase tracking-[0.24em] text-on-surface-variant md:text-start md:tracking-[0.3em]">
            © {{ date('Y') }} MEACASH. HIGH-FIDELITY DIGITAL ASSETS. ALL RIGHTS RESERVED.
        </p>
        <div class="flex flex-col gap-3 sm:flex-row sm:gap-8">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-headline text-[10px] tracking-widest uppercase text-on-surface-variant">
                    {{ __('noir.systems_operational') }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-on-surface-variant">language</span>
                <span class="font-headline text-[10px] tracking-widest uppercase text-on-surface-variant">
                    {{ strtoupper(app()->getLocale()) }} / USD
                </span>
            </div>
        </div>
    </div>
</footer>
