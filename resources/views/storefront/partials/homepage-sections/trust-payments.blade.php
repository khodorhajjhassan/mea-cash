@php
    $locale = app()->getLocale();
    $title = $section->{"title_{$locale}"} ?: $section->title_en;
    $subtitle = $section->{"subtitle_{$locale}"} ?: $section->subtitle_en;
    $settings = $section->settings ?? [];
    $badge = $settings["badge_{$locale}"] ?? $settings['badge_en'] ?? null;
    $features = collect($settings['features'] ?? [])->filter(fn ($item) => ($item["label_{$locale}"] ?? $item['label_en'] ?? null));
@endphp

<section class="px-4 py-12 md:px-8 md:py-16 relative z-10 sf-reveal-section">
    <div class="mx-auto max-w-[1440px] overflow-hidden rounded-[2rem] border border-outline-variant/15 bg-surface-container-low/55 p-5 shadow-2xl backdrop-blur-xl md:p-8">
        <div class="grid gap-8 lg:grid-cols-[0.95fr_1.35fr] lg:items-center">
            <div>
                @if($badge)
                    <span class="inline-flex items-center gap-2 rounded-full border border-primary-container/25 bg-primary-container/10 px-3 py-1 font-label text-[10px] font-black uppercase tracking-[0.22em] text-primary-container">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        {{ $badge }}
                    </span>
                @endif
                <h2 class="mt-5 font-headline text-3xl font-black uppercase tracking-tight text-on-surface md:text-5xl">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-on-surface-variant md:text-base">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($features as $item)
                    @php
                        $label = $item["label_{$locale}"] ?? $item['label_en'] ?? '';
                        $text = $item["text_{$locale}"] ?? $item['text_en'] ?? '';
                        $icon = $item['icon'] ?? 'bolt';
                    @endphp
                    <div class="group rounded-[1.35rem] border border-outline-variant/15 bg-surface-container/70 p-5 transition hover:border-primary-container/45 hover:bg-surface-container-high">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl border border-primary-container/20 bg-primary-container/10 text-primary-container shadow-[0_0_24px_rgba(0,240,255,0.1)]">
                            <span class="material-symbols-outlined">{{ $icon }}</span>
                        </div>
                        <h3 class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">{{ $label }}</h3>
                        @if($text)
                            <p class="mt-2 text-xs leading-relaxed text-on-surface-variant">{{ $text }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
