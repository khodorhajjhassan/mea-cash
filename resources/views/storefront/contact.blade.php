@extends('storefront.layouts.app')

@section('title', $title . ' - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<section class="relative px-4 py-12 md:px-8 md:py-16">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-[radial-gradient(circle_at_15%_12%,rgba(0,240,255,0.16),transparent_34%),radial-gradient(circle_at_86%_18%,rgba(254,0,254,0.12),transparent_30%)] blur-3xl"></div>

    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-[2rem] border border-outline-variant/10 bg-surface-container-low/70 p-6 shadow-[0_24px_80px_rgba(0,0,0,0.22)] backdrop-blur-xl md:p-8">
            <p class="font-label text-[10px] font-black uppercase tracking-[0.28em] text-primary-container">
                {{ $locale === 'ar' ? 'الدعم والتواصل' : 'Support Channel' }}
            </p>
            <h1 class="mt-4 font-headline text-3xl font-black uppercase tracking-tight text-on-surface sm:text-4xl">
                {{ $title }}
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-on-surface-variant">
                {{ $locale === 'ar'
                    ? 'أرسل لنا تفاصيل طلبك أو سؤالك وسيراجعه فريق MeaCash. نستخدم حماية ذكية لتقليل الرسائل المكررة والاحتيالية.'
                    : 'Send us your question or order issue and the MeaCash team will review it. We use smart checks to reduce duplicate and scam messages.' }}
            </p>

            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 rounded-2xl border border-primary-container/15 bg-primary-container/10 p-4">
                    <span class="material-symbols-outlined text-primary-container">shield_lock</span>
                    <span class="font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
                        {{ $locale === 'ar' ? 'حماية من التكرار والسبام' : 'Duplicate and spam protected' }}
                    </span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/60 p-4">
                    <span class="material-symbols-outlined text-secondary-container">schedule</span>
                    <span class="font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
                        {{ $locale === 'ar' ? 'نرد بأسرع وقت ممكن' : 'We reply as soon as possible' }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('store.contact.store') }}" class="rounded-[2rem] border border-outline-variant/10 bg-surface-container/80 p-5 shadow-[0_24px_80px_rgba(0,0,0,0.28)] backdrop-blur-xl md:p-8">
            @csrf
            <input type="text" name="company_website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'الاسم' : 'Name' }}</label>
                    <input id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required maxlength="120" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
                    @error('name') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required maxlength="160" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
                    @error('email') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="subject" class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'الموضوع' : 'Subject' }}</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" maxlength="160" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container" placeholder="{{ $locale === 'ar' ? 'مثال: مشكلة في طلب' : 'Example: Issue with an order' }}">
                @error('subject') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="message" class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'الرسالة' : 'Message' }}</label>
                <textarea id="message" name="message" rows="7" required minlength="10" maxlength="2000" class="w-full resize-y rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm leading-6 text-on-surface outline-none transition focus:border-primary-container" placeholder="{{ $locale === 'ar' ? 'اكتب التفاصيل هنا...' : 'Write the details here...' }}">{{ old('message') }}</textarea>
                @error('message') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="mt-6 inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-6 py-4 font-headline text-xs font-black uppercase tracking-[0.24em] text-on-primary-container shadow-[0_18px_50px_rgba(0,240,255,0.18)] transition hover:scale-[1.01] active:scale-[0.99]">
                <span>{{ $locale === 'ar' ? 'إرسال الرسالة' : 'Send Message' }}</span>
                <span class="material-symbols-outlined">send</span>
            </button>
        </form>
    </div>
</section>
@endsection
