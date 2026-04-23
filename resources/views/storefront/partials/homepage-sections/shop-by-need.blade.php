@php
    $locale = app()->getLocale();
    $title = $section->{"title_{$locale}"} ?: $section->title_en;
    $subtitle = $section->{"subtitle_{$locale}"} ?: $section->subtitle_en;
    $settings = $section->settings ?? [];
    $badge = $settings["badge_{$locale}"] ?? $settings['badge_en'] ?? null;
    $cards = collect($settings['cards'] ?? [])->filter(fn ($item) => ($item["title_{$locale}"] ?? $item['title_en'] ?? null));
@endphp

<section class="px-4 py-12 md:px-8 md:py-16 relative z-10 sf-reveal-section sf-lazy-section">
    <div class="mx-auto max-w-[1440px]">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                @if($badge)
                    <span class="mb-3 inline-flex rounded-full border border-secondary-container/25 bg-secondary-container/10 px-3 py-1 font-label text-[10px] font-black uppercase tracking-[0.24em] text-secondary-container">
                        {{ $badge }}
                    </span>
                @endif
                <h2 class="font-headline text-3xl font-black uppercase tracking-tight text-on-surface md:text-5xl">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-on-surface-variant md:text-base">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($cards as $card)
                @php
                    $cardTitle = $card["title_{$locale}"] ?? $card['title_en'] ?? '';
                    $cardText = $card["text_{$locale}"] ?? $card['text_en'] ?? '';
                    $icon = $card['icon'] ?? 'shopping_bag';
                    $accent = $card['accent'] ?? 'var(--color-primary-container)';
                    $url = $card['url'] ?? route('store.home');
                @endphp
                <a href="{{ $url }}" class="group relative overflow-hidden rounded-[1.5rem] border border-outline-variant/15 bg-surface-container/65 p-5 transition hover:-translate-y-1 hover:border-primary-container/45 hover:bg-surface-container-high hover:shadow-[0_24px_60px_rgba(0,0,0,0.16)]">
                    <span class="absolute end-5 top-5 h-16 w-16 rounded-full opacity-10 blur-2xl" style="background: {{ $accent }}"></span>
                    <div class="relative flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border bg-surface-container-highest shadow-lg" style="border-color: color-mix(in srgb, {{ $accent }} 38%, transparent); color: {{ $accent }};">
                            <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-headline text-lg font-black uppercase tracking-tight text-on-surface">{{ $cardTitle }}</h3>
                            @if($cardText)
                                <p class="mt-2 text-sm leading-relaxed text-on-surface-variant">{{ $cardText }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="relative mt-6 flex items-center justify-between border-t border-outline-variant/10 pt-4 font-label text-[10px] font-black uppercase tracking-[0.2em]" style="color: {{ $accent }};">
                        <span>{{ __('Explore') }}</span>
                        <span class="material-symbols-outlined text-lg transition group-hover:translate-x-1 rtl:group-hover:-translate-x-1">arrow_forward</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
