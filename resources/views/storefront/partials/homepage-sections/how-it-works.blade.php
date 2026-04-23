@php
    $locale = app()->getLocale();
    $title = $section->{"title_{$locale}"} ?: $section->title_en;
    $subtitle = $section->{"subtitle_{$locale}"} ?: $section->subtitle_en;
    $settings = $section->settings ?? [];
    $steps = collect($settings['features'] ?? [])->filter(fn ($item) => ($item["label_{$locale}"] ?? $item['label_en'] ?? null));
@endphp

<section class="px-4 md:px-8 py-16 md:py-24 relative z-10 sf-reveal-section sf-lazy-section">
    <x-noir.section-heading :title="$title" :subtitle="$subtitle" :centered="true" :gradient="true" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        @foreach($steps as $index => $step)
            @php
                $label = $step["label_{$locale}"] ?? $step['label_en'] ?? '';
                $text = $step["text_{$locale}"] ?? $step['text_en'] ?? '';
                $icon = $step['icon'] ?? 'bolt';
                $isAlt = $index % 2 === 1;
            @endphp
            <div class="glass-panel p-6 md:p-10 rounded-3xl group relative overflow-hidden transition-all duration-500 {{ $isAlt ? 'hover:border-[#fe00fe]/50' : 'hover:border-[#00f0ff]/50' }}">
                <div class="absolute top-0 {{ $locale == 'ar' ? 'left-0' : 'right-0' }} p-5 md:p-6 font-headline font-black text-5xl md:text-6xl text-on-surface-variant/5">
                    {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="w-16 h-16 rounded-2xl {{ $isAlt ? 'bg-secondary-container/10 border-secondary-container/30' : 'bg-primary-container/10 border-primary-container/30' }} flex items-center justify-center mb-8 border group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl {{ $isAlt ? 'text-secondary-container' : 'text-primary-container' }}">{{ $icon }}</span>
                </div>
                <h3 class="font-headline text-xl font-bold tracking-wider uppercase mb-4 text-on-surface {{ $isAlt ? 'group-hover:text-secondary-container' : 'group-hover:text-primary-container' }} transition-colors">
                    {{ $label }}
                </h3>
                @if($text)
                    <p class="text-on-surface-variant text-sm leading-relaxed">{{ $text }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
