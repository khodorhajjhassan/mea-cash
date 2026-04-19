@php
    /** @var \App\Models\HomepageSection $section */
    $locale = app()->getLocale();
    $title = $section->{"title_{$locale}"} ?: $section->title_en;
    $subtitle = $section->{"subtitle_{$locale}"} ?: $section->subtitle_en;
    $badge = $section->settings["badge_{$locale}"] ?? $section->settings['badge_en'] ?? null;

    $accent = match ($section->type) {
        \App\Models\HomepageSection::TYPE_FLASH_SALE => '#fe00fe',
        \App\Models\HomepageSection::TYPE_TOP_DEAL => '#fbbf24',
        \App\Models\HomepageSection::TYPE_BEST_SELLER => '#00f0ff',
        default => '#7df4ff',
    };
@endphp

<section class="px-4 md:px-8 py-12 relative z-10 sf-reveal-section">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            @if($badge)
                <span class="mb-3 inline-flex rounded-full border px-3 py-1 font-label text-[10px] font-black uppercase tracking-[0.24em]" style="border-color: {{ $accent }}55; color: {{ $accent }};">
                    {{ $badge }}
                </span>
            @endif
            <h2 class="font-headline text-3xl md:text-4xl font-black uppercase tracking-tight text-on-surface">
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-on-surface-variant">{{ $subtitle }}</p>
            @endif
        </div>

        @if($section->ends_at)
            <div class="rounded-2xl border border-outline-variant/20 bg-surface-container-low/50 px-4 py-3 text-end">
                <div class="font-label text-[9px] uppercase tracking-widest text-outline">Ends</div>
                <div class="font-headline text-sm font-black text-on-surface">{{ $section->ends_at->format('M d, H:i') }}</div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 md:gap-6">
        @foreach($items as $item)
            <x-noir.product-card :model="$item" class="animate-fade-in" />
        @endforeach
    </div>
</section>
