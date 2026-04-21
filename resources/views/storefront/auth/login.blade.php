@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'تسجيل الدخول - MeaCash' : 'Login - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

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
            <div class="sf-field">
                <label for="email">{{ $locale == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="{{ $locale == 'ar' ? 'example@email.com' : 'example@email.com' }}">
                @error('email') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="password">{{ $locale == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" id="password" required placeholder="••••••••">
                @error('password') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-outline-variant bg-surface-container-lowest text-primary-container focus:ring-primary-container">
                <label for="remember" class="text-sm text-on-surface-variant">{{ $locale == 'ar' ? 'تذكرني' : 'Remember me' }}</label>
            </div>

            <button type="submit" class="sf-auth-submit w-full">
                {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-on-surface-variant">
            {{ $locale == 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
            <a href="{{ route('store.register') }}" class="font-semibold text-primary-container hover:text-secondary-container">
                {{ $locale == 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </p>
    </div>
</div>
@endsection
