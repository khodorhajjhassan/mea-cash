@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'تسجيل الدخول - MeaCash' : 'Login - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $redirectTo = request('redirect_to');
@endphp

<div class="mx-auto max-w-md px-4 py-10 sm:py-14">
    <div class="sf-auth-card p-6 sm:p-8">
        <h1 class="text-center font-headline text-2xl font-black uppercase text-on-surface">
            {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
        </h1>
        <p class="mt-2 text-center text-sm leading-relaxed text-on-surface-variant">
            {{ $locale == 'ar' ? 'أدخل بريدك الإلكتروني وكلمة المرور.' : 'Enter your email and password.' }}
        </p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
            @csrf
            @if($redirectTo)
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <div class="sf-field">
                <label for="email">{{ $locale == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="example@email.com">
                @error('email') <p class="mt-1 text-xs" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="password">{{ $locale == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" id="password" required placeholder="••••••••">
                @error('password') <p class="mt-1 text-xs" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="text-end">
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary-container hover:text-secondary-container">
                    {{ $locale == 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                </a>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-outline-variant bg-surface-container-lowest text-primary-container focus:ring-primary-container">
                <label for="remember" class="text-sm text-on-surface-variant">{{ $locale == 'ar' ? 'تذكرني' : 'Remember me' }}</label>
            </div>

            <button type="submit" class="sf-auth-submit w-full">
                {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
            </button>
        </form>

        <div class="my-5 flex items-center gap-3 text-xs uppercase tracking-[0.22em] text-outline">
            <span class="h-px flex-1 bg-outline-variant/15"></span>
            <span>{{ $locale === 'ar' ? 'أو المتابعة باستخدام Google' : 'Or continue with Google' }}</span>
            <span class="h-px flex-1 bg-outline-variant/15"></span>
        </div>

        <a href="{{ route('login.google', ['locale' => $locale, 'redirect_to' => $redirectTo]) }}" class="flex w-full items-center justify-center gap-3 rounded-2xl border border-outline-variant/15 bg-surface-container-low px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.16em] text-on-surface transition hover:border-primary-container/50 hover:text-primary-container">
            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.7 3.4 14.6 2.5 12 2.5A9.5 9.5 0 1 0 12 21.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.5H12Z"/>
                <path fill="#34A853" d="M3.9 7.7 7.1 10c.9-2.5 2.7-4 4.9-4 1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.7 3.4 14.6 2.5 12 2.5c-3.6 0-6.8 2-8.4 5.2Z"/>
                <path fill="#FBBC05" d="M12 21.5c2.5 0 4.5-.8 6-2.2l-2.8-2.3c-.8.6-1.8 1-3.2 1-3.9 0-5.2-2.6-5.5-3.8l-3.2 2.4c1.6 3.2 4.9 4.9 8.7 4.9Z"/>
                <path fill="#4285F4" d="M3.3 16.6 6.6 14c-.2-.6-.3-1.3-.3-2s.1-1.4.3-2L3.3 7.7C2.8 8.9 2.5 10.4 2.5 12s.3 3.1.8 4.6Z"/>
            </svg>
            <span>{{ $locale === 'ar' ? 'المتابعة باستخدام Google' : 'Continue with Google' }}</span>
        </a>

        <p class="mt-6 text-center text-sm text-on-surface-variant">
            {{ $locale == 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
            <a href="{{ route('store.register', ['locale' => $locale, 'redirect_to' => $redirectTo]) }}" class="font-semibold text-primary-container hover:text-secondary-container">
                {{ $locale == 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </p>
    </div>
</div>
@endsection
