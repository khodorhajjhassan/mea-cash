@extends('storefront.layouts.app')

@section('title', __('Login'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 relative overflow-hidden">
    {{-- Decorative Background --}}
    <div class="absolute top-1/4 start-1/4 w-96 h-96 light-leak-cyan opacity-20 blur-[100px] pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-1/4 end-1/4 w-96 h-96 light-leak-magenta opacity-20 blur-[100px] pointer-events-none animate-pulse"></div>

    <div class="w-full max-w-md relative z-10 animate-fade-in">
        <div class="glass-panel p-8 md:p-12 rounded-[32px] border-primary-container/20 shadow-[0_32px_128px_rgba(0,0,0,0.8)]">
            {{-- Header --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-surface-container-highest border border-outline-variant/30 mb-6 shadow-xl">
                    <img src="{{ asset('meacash-logo.png') }}" alt="MeaCash" class="h-10 w-auto">
                </div>
                <h1 class="font-headline text-3xl font-black italic tracking-tighter uppercase mb-2">
                    {{ __('noir.welcome_back') ?? 'WELCOME BACK' }}
                </h1>
                <p class="text-on-surface-variant text-sm font-label tracking-widest uppercase opacity-60">
                    {{ __('noir.login_to_account') ?? 'Access your digital vault' }}
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                {{-- Email --}}
                <div class="space-y-2">
                    <label class="font-label text-xs font-bold uppercase tracking-widest text-on-surface/70 ps-2" for="email">
                        {{ __('Email Address') }}
                    </label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 start-4 flex items-center text-outline group-focus-within:text-primary-container transition-colors">
                            <span class="material-symbols-outlined text-xl">alternate_email</span>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-surface-container-lowest border border-outline-variant/20 rounded-2xl py-4 ps-12 pe-4 text-on-surface placeholder:text-outline/30 focus:border-primary-container/40 focus:ring-4 focus:ring-primary-container/5 transition-all outline-none">
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center ps-2">
                        <label class="font-label text-xs font-bold uppercase tracking-widest text-on-surface/70" for="password">
                            {{ __('Password') }}
                        </label>
                    </div>
                    <div class="relative group">
                        <span class="absolute inset-y-0 start-4 flex items-center text-outline group-focus-within:text-primary-container transition-colors">
                            <span class="material-symbols-outlined text-xl">lock</span>
                        </span>
                        <input id="password" type="password" name="password" required
                               class="w-full bg-surface-container-lowest border border-outline-variant/20 rounded-2xl py-4 ps-12 pe-4 text-on-surface placeholder:text-outline/30 focus:border-primary-container/40 focus:ring-4 focus:ring-primary-container/5 transition-all outline-none">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary-container hover:bg-primary-container-hover text-on-primary-container font-headline font-black py-4 rounded-2xl flex items-center justify-center gap-3 transition-all active:scale-[0.98] shadow-[0_8px_32px_rgba(0,240,255,0.3)] group">
                        <span>{{ __('noir.login_cta') ?? 'INITIALIZE SESSION' }}</span>
                        <span class="material-symbols-outlined transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1">login</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center pt-8 border-t border-outline-variant/10">
                <p class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant opacity-60">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-primary-container hover:text-secondary-container ms-2">
                        {{ __('Request Access') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
