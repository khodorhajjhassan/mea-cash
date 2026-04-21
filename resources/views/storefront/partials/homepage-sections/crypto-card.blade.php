@php
    $locale = app()->getLocale();
    $title = $section->{"title_{$locale}"} ?: $section->title_en;
    $subtitle = $section->{"subtitle_{$locale}"} ?: $section->subtitle_en;
    $settings = $section->settings ?? [];
    $badge = $settings["badge_{$locale}"] ?? $settings['badge_en'] ?? null;
    $buttonText = $settings["button_text_{$locale}"] ?? $settings['button_text_en'] ?? __('Get started');
    $buttonUrl = $settings['button_url'] ?? route('store.home').'#products-section';
    $status = $settings["status_{$locale}"] ?? $settings['status_en'] ?? __('Active');
    $amount = $settings["amount_label_{$locale}"] ?? $settings['amount_label_en'] ?? '$2,450';
    $cardBrand = $settings["card_brand_{$locale}"] ?? $settings['card_brand_en'] ?? 'MEACASH CARD';
    $cardKind = $settings["card_kind_{$locale}"] ?? $settings['card_kind_en'] ?? 'CRYPTO WALLET';
    $cardHolder = $settings["card_holder_{$locale}"] ?? $settings['card_holder_en'] ?? 'MEACASH USER';
    $features = collect($settings['features'] ?? [])->filter(fn ($item) => ($item["label_{$locale}"] ?? $item['label_en'] ?? null));
@endphp

<section class="px-4 py-8 md:px-8 md:py-14 relative z-10 sf-reveal-section">
    <div class="sf-crypto-card-section mx-auto max-w-[1440px] overflow-hidden rounded-[1.5rem] border border-outline-variant/15 px-4 py-6 shadow-2xl sm:rounded-[2rem] md:px-8 md:py-8 lg:px-10">
        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
            <div class="relative z-10 py-2 lg:py-8">
                @if($badge)
                    <span class="inline-flex items-center gap-2 rounded-full border border-primary-container/25 bg-primary-container/10 px-3 py-1 font-label text-[10px] font-black uppercase tracking-[0.24em] text-primary-container">
                        <span class="material-symbols-outlined text-sm">account_balance_wallet</span>
                        {{ $badge }}
                    </span>
                @endif

                <h2 class="mt-5 max-w-lg font-headline text-4xl font-black leading-tight tracking-tight text-on-surface sm:text-5xl md:text-6xl">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-on-surface-variant sm:text-base">{{ $subtitle }}</p>
                @endif

                @if($features->isNotEmpty())
                    <div class="mt-6 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:gap-x-5 sm:gap-y-3">
                        @foreach($features as $feature)
                            @php
                                $label = $feature["label_{$locale}"] ?? $feature['label_en'] ?? '';
                                $icon = $feature['icon'] ?? 'check_circle';
                            @endphp
                            <span class="inline-flex min-w-0 items-center gap-2 text-xs font-medium text-on-surface-variant sm:text-sm">
                                <span class="material-symbols-outlined text-base text-primary-container">{{ $icon }}</span>
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <a href="{{ $buttonUrl }}" class="mt-7 inline-flex w-full items-center justify-center gap-3 rounded-full bg-primary-container px-6 py-3 font-headline text-sm font-black text-on-primary-container shadow-[0_16px_36px_rgba(0,229,255,0.18)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_48px_rgba(0,229,255,0.24)] sm:w-auto">
                    {{ $buttonText }}
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
            </div>

            <div class="sf-crypto-stage relative flex min-h-[255px] items-center justify-center pt-3 sm:min-h-[310px] md:min-h-[360px] lg:min-h-[390px] lg:pt-0">
                <div class="sf-crypto-visual-wrap relative z-10 w-full max-w-[540px] px-0 sm:px-8">
                    <span class="sf-crypto-status absolute end-2 top-2 z-20 inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-label text-[9px] font-black uppercase tracking-widest sm:-top-10 sm:end-0 sm:px-4 sm:py-2 sm:text-[10px]">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ $status }}
                    </span>

                    <div class="sf-crypto-visual-card relative mx-auto w-full max-w-[430px] rounded-[1.25rem] p-4 shadow-[0_28px_70px_rgba(0,0,0,0.26)] sm:rounded-[1.65rem] sm:p-6 md:p-7" data-tilt-card>
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-headline text-xs font-black uppercase tracking-[0.16em] text-white/90 sm:text-sm sm:tracking-[0.18em]">{{ $cardBrand }}</div>
                                <div class="mt-1 font-label text-[8px] font-black uppercase tracking-widest text-white/45 sm:text-[10px]">{{ $cardKind }}</div>
                            </div>
                            <div class="h-7 w-10 rounded-lg bg-gradient-to-br from-yellow-300 to-amber-500 shadow-inner sm:h-9 sm:w-12 sm:rounded-xl"></div>
                        </div>

                        <div class="mt-8 flex items-center gap-2 font-headline text-sm font-black tracking-[0.2em] text-white/72 sm:mt-12 sm:gap-4 sm:text-lg sm:tracking-[0.35em]">
                            <span>&bull;&bull;&bull;&bull;</span>
                            <span>&bull;&bull;&bull;&bull;</span>
                            <span>&bull;&bull;&bull;&bull;</span>
                            <span>4291</span>
                        </div>

                        <div class="mt-8 flex items-end justify-between sm:mt-10">
                            <div>
                                <div class="font-label text-[8px] font-black uppercase tracking-widest text-white/40 sm:text-[9px]">{{ __('Card Holder') }}</div>
                                <div class="mt-1 font-headline text-[10px] font-black uppercase tracking-widest text-white/85 sm:text-xs">{{ $cardHolder }}</div>
                            </div>
                            <div class="flex -space-x-2">
                                <span class="h-6 w-6 rounded-full bg-rose-500/90 sm:h-8 sm:w-8"></span>
                                <span class="h-6 w-6 rounded-full bg-amber-500/90 sm:h-8 sm:w-8"></span>
                            </div>
                        </div>
                    </div>

                    <div class="sf-crypto-amount absolute bottom-2 start-3 z-20 inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-headline text-[10px] font-black sm:-bottom-9 sm:start-10 sm:px-4 sm:py-2 sm:text-xs">
                        <span class="material-symbols-outlined text-sm">payments</span>
                        {{ $amount }}
                    </div>

                    <div class="sf-crypto-rail absolute -end-4 top-1/2 hidden h-40 w-1 -translate-y-1/2 rounded-full sm:block"></div>
                </div>
            </div>
        </div>
    </div>
</section>
