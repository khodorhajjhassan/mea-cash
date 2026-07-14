@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'استعادة كلمة المرور - MeaCash' : 'Forgot Password - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="mx-auto max-w-md px-4 py-10 sm:py-14">
    <div class="sf-auth-card p-6 sm:p-8">
        <h1 class="text-center font-headline text-2xl font-black uppercase text-on-surface">
            {{ $locale === 'ar' ? 'استعادة كلمة المرور' : 'Recover Password' }}
        </h1>
        <p class="mt-2 text-center text-sm leading-relaxed text-on-surface-variant">
            {{ $locale === 'ar' ? 'أدخل بريدك الإلكتروني وسنرسل لك رمز تحقق لإعادة التعيين.' : 'Enter your email and we will send a verification code to reset your password.' }}
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf
            <div class="sf-field">
                <label for="email">{{ $locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required autofocus>
                @error('email') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="sf-auth-submit w-full">
                {{ $locale === 'ar' ? 'إرسال الرمز' : 'Send Code' }}
            </button>
        </form>
    </div>
</div>
@endsection
