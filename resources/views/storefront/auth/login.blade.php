@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'تسجيل الدخول - MeaCash' : 'Login - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-12 max-w-md mx-auto">
    <div class="sf-panel p-6 sm:p-8" style="border-radius: var(--sf-radius-xl);">
        <h1 class="text-xl font-bold font-heading text-center" style="color: var(--sf-text);">
            {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
        </h1>
        <p class="mt-2 text-sm text-center" style="color: var(--sf-muted);">
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
                <input type="checkbox" name="remember" id="remember" class="rounded" style="accent-color: var(--sf-gold);">
                <label for="remember" class="text-sm" style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'تذكرني' : 'Remember me' }}</label>
            </div>

            <button type="submit" class="sf-btn-gold w-full" style="height: 3rem;">
                {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
            </button>
        </form>

        <p class="mt-6 text-sm text-center" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'ليس لديك حساب؟' : "Don't have an account?" }}
            <a href="{{ route('store.register') }}" class="font-semibold" style="color: var(--sf-gold-light);">
                {{ $locale == 'ar' ? 'إنشاء حساب' : 'Register' }}
            </a>
        </p>
    </div>
</div>
@endsection
