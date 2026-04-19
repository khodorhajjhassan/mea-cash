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
</section>
@endsection
