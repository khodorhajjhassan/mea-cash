@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'إنشاء حساب - MeaCash' : 'Register - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-12 max-w-md mx-auto">
    <div class="sf-panel p-6 sm:p-8" style="border-radius: var(--sf-radius-xl);">
        <h1 class="text-xl font-bold font-heading text-center" style="color: var(--sf-text);">
            {{ $locale == 'ar' ? 'إنشاء حساب جديد' : 'Create Account' }}
        </h1>
        <p class="mt-2 text-sm text-center" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'أنشئ حسابك للبدء بالشراء.' : 'Create your account to start shopping.' }}
        </p>

        <form method="POST" action="{{ route('store.register.store') }}" class="mt-6 space-y-4">
            @csrf
            <div class="sf-field">
                <label for="name">{{ $locale == 'ar' ? 'الاسم الكامل' : 'Full Name' }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus>
                @error('name') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="email">{{ $locale == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="phone">{{ $locale == 'ar' ? 'رقم الهاتف (اختياري)' : 'Phone (optional)' }}</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+961...">
            </div>

            <div class="sf-field">
                <label for="password">{{ $locale == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" id="password" required placeholder="{{ $locale == 'ar' ? '8 أحرف على الأقل' : 'At least 8 characters' }}">
                @error('password') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="password_confirmation">{{ $locale == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <input type="hidden" name="preferred_language" value="{{ $locale }}">

            <button type="submit" class="sf-btn-gold w-full" style="height: 3rem;">
                {{ $locale == 'ar' ? 'إنشاء حساب' : 'Create Account' }}
            </button>
        </form>

        <p class="mt-6 text-sm text-center" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
            <a href="{{ route('login') }}" class="font-semibold" style="color: var(--sf-gold-light);">
                {{ $locale == 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
            </a>
        </p>
    </div>
</div>
@endsection
