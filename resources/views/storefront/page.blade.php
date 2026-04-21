@extends('storefront.layouts.app')

@section('title', $title . ' - MeaCash')

@section('content')
<section class="relative px-4 py-12 md:px-8 md:py-16">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_20%_10%,rgba(0,240,255,0.12),transparent_32%),radial-gradient(circle_at_82%_8%,rgba(254,0,254,0.1),transparent_30%)] blur-3xl"></div>

    <div class="mx-auto max-w-4xl">
        <div class="mb-8">
            <p class="font-label text-[10px] font-black uppercase tracking-[0.28em] text-primary-container">
                {{ __('CMS Page') }}
            </p>
            <h1 class="mt-3 font-headline text-3xl font-black uppercase tracking-tight text-on-surface sm:text-4xl md:text-5xl">
                {{ $title }}
            </h1>
        </div>

        <article class="glass-panel prose prose-invert max-w-none rounded-[2rem] border-outline-variant/10 p-6 text-on-surface-variant md:p-10">
            @if(filled($content))
                {!! $content !!}
            @else
                <p>{{ __('This page content is being prepared. Please check again soon.') }}</p>
            @endif
        </article>
    </div>

    @if(($aboutSections ?? collect())->isNotEmpty())
        <div class="mt-8">
            @foreach($aboutSections as $homepageSection)
                @include('storefront.partials.homepage-section', $homepageSection)
            @endforeach
        </div>
    @endif

    @if($slug === 'about')
        <section class="px-4 md:px-8 py-16 md:py-20 relative sf-reveal-section">
            <div class="grid grid-cols-12 gap-8 items-center max-w-7xl mx-auto">
                <div class="col-span-12 lg:col-span-5 order-2 lg:order-1 text-start">
                    <h2 class="font-headline text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-8 uppercase">
                        {{ __('noir.secure_assets_title') }} <br /><span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f0ff] to-[#fe00fe]">{{ __('noir.secure_assets_subtitle') }}</span>
                    </h2>
                    <p class="text-on-surface-variant text-base md:text-lg leading-relaxed mb-10">
                        {{ __('noir.secure_assets_desc') }}
                    </p>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary-container/10 flex items-center justify-center border border-primary-container/20">
                                <span class="material-symbols-outlined text-primary-container">speed</span>
                            </div>
                            <div>
                                <h4 class="font-headline text-sm font-bold uppercase tracking-widest text-on-surface">Ultra Fast Delivery</h4>
                                <p class="text-xs text-on-surface-variant/60">Average delivery time: 14 seconds</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-secondary-container/10 flex items-center justify-center border border-secondary-container/20">
                                <span class="material-symbols-outlined text-secondary-container">security</span>
                            </div>
                            <div>
                                <h4 class="font-headline text-sm font-bold uppercase tracking-widest text-on-surface">Vanguard Security</h4>
                                <p class="text-xs text-on-surface-variant/60">Military grade encryption protocols</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-7 order-1 lg:order-2 flex justify-end">
                    <div class="relative w-full aspect-video rounded-3xl overflow-hidden glass-panel p-1">
                        <img class="w-full h-full object-cover rounded-2xl"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRQAeS2ZAoyahfL86240LJ26Ih0F4jliE9yad411iOINSt87VdX-bC4PHrU3QM1Adrs0iP357z99aoZCjSU3nFCwUWmTM_s2eT7SuL7kOi_DKdnxIM6GIOY-2X_tF4VSNlfVXvriKPLiCfXsKRvkQWvt859u-jl6ttPOLclU_DS4fM30A_DKjIq0EMg9EYY-npcbrfJe_2EYoFqxxmkHvPmM8_zgN48QvwTGRH6HlTqawlZ2Zy8GgGWkv24yDT8m5RKMpWX3x4Nd4"
                            alt="Security Visualization">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary-container/20 to-transparent pointer-events-none"></div>
                    </div>
                </div>
            </div>
        </section>

        @if(($featuredFeedbacks ?? collect())->isNotEmpty())
            <section class="py-16 md:py-24 border-y border-outline-variant/10 bg-surface-container-low/30 relative overflow-hidden sf-reveal-section">
                <div class="px-4 md:px-8 mb-12">
                    <x-noir.section-heading :title="__('What Our Community Says')" :subtitle="__('User Reviews')" :centered="true" :gradient="true" />
                </div>

                <div class="overflow-hidden">
                    <div class="{{ app()->getLocale() === 'ar' ? 'animate-marquee-rtl' : 'animate-marquee' }} flex w-max items-stretch gap-6 px-10" dir="ltr">
                        @php
                            $feedbackCards = $featuredFeedbacks->count() < 6 ? $featuredFeedbacks->concat($featuredFeedbacks) : $featuredFeedbacks;
                        @endphp
                        @foreach($feedbackCards as $fb)
                            <div class="sf-feedback-card group w-72 md:w-96 p-6 rounded-3xl border border-outline-variant/15 bg-surface-container/60 backdrop-blur-xl flex flex-col justify-between transition-all duration-500 hover:border-primary-container/40 hover:bg-surface-container-high hover:shadow-[0_0_40px_rgba(0,240,255,0.08)]">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-container/20 to-secondary-container/20 border border-outline-variant/30 flex items-center justify-center font-headline font-black text-primary-container">
                                                {{ substr($fb->user?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="truncate font-headline text-sm font-black text-on-surface">{{ $fb->user?->name ?? 'Anonymous' }}</div>
                                                <div class="text-[9px] uppercase tracking-widest text-outline">{{ __('Verified Buyer') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm italic leading-relaxed text-on-surface-variant line-clamp-4">"{{ $fb->comment ?: 'Excellent and very fast service!' }}"</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if(($faqs ?? collect())->isNotEmpty())
            <section class="px-4 md:px-8 py-24 bg-surface-container-lowest/30 relative z-10 sf-reveal-section" id="faq-section">
                <div class="max-w-4xl mx-auto">
                    <x-noir.section-heading :title="__('noir.common_queries')" :centered="true" />

                    <div class="space-y-4">
                        @foreach($faqs as $faq)
                            <details class="group bg-surface-container/40 rounded-2xl border border-outline-variant/10 overflow-hidden">
                                <summary class="flex justify-between items-center p-6 cursor-pointer hover:bg-surface-container-high transition-colors list-none">
                                    <span class="font-headline font-bold text-sm md:text-base uppercase tracking-widest text-on-surface">
                                        {{ $faq->{"question_".app()->getLocale()} }}
                                    </span>
                                    <span class="material-symbols-outlined expand-icon transition-transform duration-300 text-primary-container">expand_more</span>
                                </summary>
                                <div class="px-6 pb-6 pt-2 animate-fade-in">
                                    <p class="text-on-surface-variant text-sm leading-relaxed">
                                        {!! nl2br(e($faq->{"answer_".app()->getLocale()})) !!}
                                    </p>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif
</section>
@endsection
