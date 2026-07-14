@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'إعادة تعيين كلمة المرور - MeaCash' : 'Reset Password - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="mx-auto max-w-md px-4 py-10 sm:py-14">
    <div class="sf-auth-card p-6 sm:p-8">
        <h1 class="text-center font-headline text-2xl font-black uppercase text-on-surface">
            {{ $locale === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
        </h1>
        <p class="mt-2 text-center text-sm leading-relaxed text-on-surface-variant">
            {{ $locale === 'ar' ? 'أدخل الرمز الذي وصلك مع كلمة المرور الجديدة.' : 'Enter the code you received along with your new password.' }}
        </p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            <div class="sf-field">
                <label for="email">{{ $locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required>
            </div>

            <div class="sf-field">
                <label for="code">{{ $locale === 'ar' ? 'رمز التحقق' : 'Verification Code' }}</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" inputmode="numeric" maxlength="6" required>
                @error('code') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="password">{{ $locale === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }}</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="sf-field">
                <label for="password_confirmation">{{ $locale === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <button type="submit" class="sf-auth-submit w-full">
                {{ $locale === 'ar' ? 'تحديث كلمة المرور' : 'Update Password' }}
            </button>
        </form>
    </div>
</div>
@endsection
