@extends('storefront.layouts.app')

@php $locale = app()->getLocale(); @endphp

@section('content')
<div class="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-12">
    <div class="sf-auth-card w-full rounded-[2rem] border border-outline-variant/15 bg-surface-container/70 p-6 shadow-2xl backdrop-blur-xl">
        <p class="font-label text-[10px] font-black uppercase tracking-[0.28em] text-primary-container">
            {{ $locale === 'ar' ? 'تأكيد البريد' : 'Email Verification' }}
        </p>
        <h1 class="mt-3 font-headline text-3xl font-black uppercase tracking-tight text-on-surface">
            {{ $locale === 'ar' ? 'أدخل رمز التأكيد' : 'Enter Verification Code' }}
        </h1>
        <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">
            {{ $locale === 'ar' ? 'أرسلنا رمزاً من 6 أرقام إلى:' : 'We sent a 6-digit code to:' }}
            <span class="font-semibold text-on-surface">{{ $email }}</span>
        </p>

        <form method="POST" action="{{ route('store.register.verify.store') }}" class="mt-6 space-y-4">
            @csrf
            <div class="sf-field">
                <label for="code">{{ $locale === 'ar' ? 'رمز التأكيد' : 'Verification Code' }}</label>
                <input id="code" name="code" value="{{ old('code') }}" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus class="text-center font-headline text-2xl font-black tracking-[0.35em]">
                @error('code')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="sf-auth-submit w-full rounded-full bg-primary-container px-6 py-3 font-headline text-sm font-black text-on-primary-container">
                {{ $locale === 'ar' ? 'تأكيد الحساب' : 'Verify Account' }}
            </button>
        </form>

        <form method="POST" action="{{ route('store.register.resend') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="font-label text-[10px] font-black uppercase tracking-widest text-primary-container">
                {{ $locale === 'ar' ? 'إرسال رمز جديد' : 'Send New Code' }}
            </button>
        </form>
    </div>
</div>
@endsection
