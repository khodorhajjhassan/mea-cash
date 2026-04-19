@props(['title', 'subtitle' => null, 'gradient' => null, 'centered' => false])

<div {{ $attributes->merge(['class' => ($centered ? 'text-center' : '') . ' mb-12 animate-fade-in']) }}>
    @if($subtitle)
        <span
            class="font-label text-primary-container tracking-[0.3em] uppercase text-xs md:text-sm mb-4 block animate-fade-in-up">
            {{ $subtitle }}
        </span>
    @endif

    <h2 class="font-headline text-3xl md:text-5xl font-black italic tracking-tighter uppercase leading-tight">
        @if($gradient)
            @php
                $parts = explode(' ', $title);
                $lastWord = array_pop($parts);
                $firstPart = implode(' ', $parts);
            @endphp
            {{ $firstPart }} <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f0ff] to-[#fe00fe] px-2">{{ $lastWord }}</span>
        @else
            {{ $title }}
        @endif
    </h2>

    @if($centered)
        <div class="h-1 w-24 bg-gradient-to-r from-primary-container to-secondary-container mx-auto mt-4 rounded-full">
        </div>
    @endif
</div>