@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'Ø­Ø³Ø§Ø¨ÙŠ - MeaCash' : 'Profile - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $initials = collect(explode(' ', trim($user->name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
    $signedInWithGoogle = $user->auth_provider === 'google';
@endphp

<div class="relative mx-auto max-w-6xl px-4 py-10 md:px-8">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_20%_20%,rgba(0,240,255,0.18),transparent_32%),radial-gradient(circle_at_80%_10%,rgba(254,0,254,0.12),transparent_28%)] blur-3xl"></div>

    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="font-label text-[10px] font-black uppercase tracking-[0.28em] text-primary-container">
                {{ $locale === 'ar' ? 'Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø­Ø³Ø§Ø¨' : 'Account Settings' }}
            </p>
            <h1 class="mt-2 font-headline text-3xl font-black uppercase tracking-tight text-on-surface sm:text-4xl md:text-5xl">
                {{ $locale === 'ar' ? 'Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø´Ø®ØµÙŠ' : 'Profile' }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-on-surface-variant">
                {{ $locale === 'ar' ? 'Ø­Ø¯Ù‘Ø« Ù…Ø¹Ù„ÙˆÙ…Ø§ØªÙƒ Ø§Ù„Ø£Ø³Ø§Ø³ÙŠØ© ÙˆØªÙØ¶ÙŠÙ„Ø§Øª Ø§Ù„Ù„ØºØ© Ù…Ù† Ù…ÙƒØ§Ù† ÙˆØ§Ø­Ø¯.' : 'Keep your basic account details and language preference up to date.' }}
            </p>
        </div>

        <a href="{{ route('store.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-5 py-3 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant transition hover:border-primary-container/50 hover:text-primary-container">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>{{ $locale === 'ar' ? 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…' : 'Dashboard' }}</span>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.4fr]">
        <aside class="rounded-[2rem] border border-outline-variant/15 bg-surface-container-low/80 p-6 shadow-2xl shadow-black/20">
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl border border-primary-container/30 bg-primary-container/10 font-headline text-2xl font-black uppercase text-primary-container shadow-[0_0_35px_rgba(0,240,255,0.18)]">
                    {{ $initials ?: 'MC' }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate font-headline text-xl font-black uppercase text-on-surface">{{ $user->name }}</h2>
                    <p class="truncate text-sm text-on-surface-variant">{{ $user->email }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/40 p-4">
                    <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'Ø±Ù‚Ù… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…' : 'User ID' }}</div>
                    <div class="mt-1 font-headline text-lg font-black text-on-surface">#{{ $user->id }}</div>
                </div>
                <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/40 p-4">
                    <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'Ø§Ù„Ø¨Ø±ÙŠØ¯ Ø§Ù„Ø¥Ù„ÙƒØªØ±ÙˆÙ†ÙŠ' : 'Email' }}</div>
                    <div class="mt-1 truncate text-sm font-bold text-on-surface">{{ $user->email }}</div>
                    @if($signedInWithGoogle)
                        <p class="mt-2 inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/10 px-3 py-1 text-[11px] font-bold text-emerald-400">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span>{{ $locale === 'ar' ? 'Ù…ØªØ­Ù‚Ù‚ Ø¹Ø¨Ø± Google' : 'Verified through Google' }}</span>
                        </p>
                        <p class="mt-2 text-xs text-on-surface-variant">{{ $locale === 'ar' ? 'Ù‡Ø°Ø§ Ø§Ù„Ø­Ø³Ø§Ø¨ Ø³Ø¬Ù„ Ø§Ù„Ø¯Ø®ÙˆÙ„ Ø¨Ø§Ø³ØªØ®Ø¯Ø§Ù… GoogleØŒ Ù„Ø°Ù„Ùƒ Ù„Ø§ ÙŠØ­ØªØ§Ø¬ Ø¥Ù„Ù‰ ØªØ­Ù‚Ù‚ Ø¨Ø±ÙŠØ¯ ÙŠØ¯ÙˆÙŠ.' : 'This account uses Google sign-in, so no manual email verification is needed.' }}</p>
                    @else
                        <p class="mt-1 text-xs text-on-surface-variant">{{ $locale === 'ar' ? 'Ù„Ø§ ÙŠÙ…ÙƒÙ† ØªØºÙŠÙŠØ±Ù‡ Ø­Ø§Ù„ÙŠØ§.' : 'Email changes are currently locked.' }}</p>
                    @endif
                </div>
            </div>
        </aside>

        <section class="rounded-[2rem] border border-outline-variant/15 bg-surface-container/80 p-5 shadow-2xl shadow-black/20 md:p-8">
            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-primary-container/20 bg-primary-container/10 px-4 py-3 text-sm font-bold text-primary-container">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('store.profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="sf-field">
                        <label for="name">{{ $locale === 'ar' ? 'Ø§Ù„Ø§Ø³Ù… Ø§Ù„ÙƒØ§Ù…Ù„' : 'Full Name' }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="sf-field">
                        <label for="phone">{{ $locale === 'ar' ? 'Ø±Ù‚Ù… Ø§Ù„Ù‡Ø§ØªÙ' : 'Phone Number' }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+961...">
                        @error('phone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="sf-field">
                    <label for="preferred_language">{{ $locale === 'ar' ? 'Ø§Ù„Ù„ØºØ© Ø§Ù„Ù…ÙØ¶Ù„Ø©' : 'Preferred Language' }}</label>
                    <select name="preferred_language" id="preferred_language">
                        <option value="en" @selected(old('preferred_language', $user->preferred_language) === 'en')>English</option>
                        <option value="ar" @selected(old('preferred_language', $user->preferred_language) === 'ar')>Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©</option>
                    </select>
                    @error('preferred_language') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                    <span>{{ $locale === 'ar' ? 'Ø­ÙØ¸ Ø§Ù„ØªØºÙŠÙŠØ±Ø§Øª' : 'Save Changes' }}</span>
                    <span class="material-symbols-outlined text-lg">check</span>
                </button>
            </form>

            <div class="my-7 h-px bg-outline-variant/15"></div>

            <a href="{{ route('password.request') }}" class="mb-4 flex w-full items-center justify-center gap-2 rounded-2xl border border-primary-container/20 bg-primary-container/10 px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.18em] text-primary-container transition hover:border-primary-container/50 hover:bg-primary-container/20">
                <span>{{ $locale === 'ar' ? 'Ø¥Ø¹Ø§Ø¯Ø© ØªØ¹ÙŠÙŠÙ† ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ±' : 'Reset Password' }}</span>
                <span class="material-symbols-outlined text-lg">password</span>
            </a>

            <form action="{{ route('store.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-error/25 bg-error-container/10 px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.18em] text-error transition hover:border-error/50 hover:bg-error-container/20">
                    <span>{{ $locale === 'ar' ? 'ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø®Ø±ÙˆØ¬' : 'Sign Out' }}</span>
                    <span class="material-symbols-outlined text-lg">logout</span>
                </button>
            </form>
        </section>
    </div>
</div>
@endsection
