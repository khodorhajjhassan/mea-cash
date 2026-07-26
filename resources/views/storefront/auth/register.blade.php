@extends('storefront.layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'إنشاء حساب - MeaCash' : 'Register - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $redirectTo = request('redirect_to');
@endphp

<div class="mx-auto max-w-2xl px-4 py-10 sm:py-14">
    <div class="sf-auth-card overflow-hidden">
        <div class="border-b border-outline-variant/10 p-6 text-center sm:p-8">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl border border-primary-container/25 bg-primary-container/10 text-primary-container">
                <span class="material-symbols-outlined">person_add</span>
            </div>
            <h1 class="font-headline text-2xl font-black uppercase text-on-surface sm:text-3xl">
                {{ $isAr ? 'إنشاء حساب جديد' : 'Create Account' }}
            </h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-on-surface-variant">
                {{ $isAr ? 'أنشئ حسابك، أكد بريدك الإلكتروني، وابدأ الشراء من محفظتك بأمان.' : 'Create your account, verify your email, and start shopping securely from your wallet.' }}
            </p>
        </div>

        <form method="POST" action="{{ route('store.register.store') }}" class="p-6 sm:p-8">
            @csrf
            @if($redirectTo)
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sf-field sm:col-span-2">
                    <label for="name">{{ $isAr ? 'الاسم الكامل' : 'Full Name' }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder="{{ $isAr ? 'اكتب اسمك الكامل' : 'Your full name' }}">
                    @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="sf-field">
                    <label for="email">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="sf-field">
                    <label for="phone">{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="{{ $isAr ? 'اختياري' : 'Optional' }}">
                    @error('phone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="sf-field">
                    <label for="password">{{ $isAr ? 'كلمة المرور' : 'Password' }}</label>
                    <input type="password" name="password" id="password" required placeholder="{{ $isAr ? '8 أحرف على الأقل' : 'At least 8 characters' }}">
                    @error('password') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="sf-field">
                    <label for="password_confirmation">{{ $isAr ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="{{ $isAr ? 'أعد كتابة كلمة المرور' : 'Repeat password' }}">
                </div>

                <div class="sf-field sm:col-span-2">
                    <label for="preferred_language">{{ $isAr ? 'اللغة المفضلة للرسائل' : 'Preferred Email Language' }}</label>
                    <select name="preferred_language" id="preferred_language" required>
                        <option value="en" @selected(old('preferred_language', $locale) === 'en')>English</option>
                        <option value="ar" @selected(old('preferred_language', $locale) === 'ar')>العربية</option>
                    </select>
                    @error('preferred_language') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <label for="terms" class="mt-5 flex cursor-pointer items-start gap-3 rounded-2xl border border-outline-variant/15 bg-surface-container-low/50 p-4 text-sm leading-relaxed text-on-surface-variant transition hover:border-primary-container/40 hover:bg-primary-container/5">
                <input type="checkbox" name="terms" id="terms" value="1" required @checked(old('terms')) class="mt-1 h-4 w-4 rounded border-outline-variant bg-surface-container-lowest text-primary-container focus:ring-primary-container">
                <span>
                    {{ $isAr ? 'أوافق على' : 'I agree to the' }}
                    <a href="{{ route('store.page', ['slug' => 'terms-and-conditions']) }}" target="_blank" class="font-semibold text-primary-container hover:text-secondary-container">
                        {{ $isAr ? 'الشروط والأحكام' : 'Terms and Conditions' }}
                    </a>
                    {{ $isAr ? 'و' : 'and the' }}
                    <a href="{{ route('store.page', ['slug' => 'privacy-policy']) }}" target="_blank" class="font-semibold text-primary-container hover:text-secondary-container">
                        {{ $isAr ? 'سياسة الخصوصية' : 'Privacy Policy' }}
                    </a>
                    .
                </span>
            </label>
            @error('terms') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror

            <button type="submit" class="sf-auth-submit mt-6 w-full">
                {{ $isAr ? 'إنشاء الحساب' : 'Create Account' }}
            </button>

            <div class="mt-5 flex items-center gap-3 text-xs uppercase tracking-[0.22em] text-outline">
                <span class="h-px flex-1 bg-outline-variant/15"></span>
                <span>{{ $isAr ? 'أو المتابعة باستخدام Google' : 'Or continue with Google' }}</span>
                <span class="h-px flex-1 bg-outline-variant/15"></span>
            </div>

            <a href="{{ route('login.google', ['locale' => $locale, 'redirect_to' => $redirectTo]) }}" class="mt-5 flex w-full items-center justify-center gap-3 rounded-2xl border border-outline-variant/15 bg-surface-container-low px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.16em] text-on-surface transition hover:border-primary-container/50 hover:text-primary-container">
                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.7 3.4 14.6 2.5 12 2.5A9.5 9.5 0 1 0 12 21.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.5H12Z"/>
                    <path fill="#34A853" d="M3.9 7.7 7.1 10c.9-2.5 2.7-4 4.9-4 1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.7 3.4 14.6 2.5 12 2.5c-3.6 0-6.8 2-8.4 5.2Z"/>
                    <path fill="#FBBC05" d="M12 21.5c2.5 0 4.5-.8 6-2.2l-2.8-2.3c-.8.6-1.8 1-3.2 1-3.9 0-5.2-2.6-5.5-3.8l-3.2 2.4c1.6 3.2 4.9 4.9 8.7 4.9Z"/>
                    <path fill="#4285F4" d="M3.3 16.6 6.6 14c-.2-.6-.3-1.3-.3-2s.1-1.4.3-2L3.3 7.7C2.8 8.9 2.5 10.4 2.5 12s.3 3.1.8 4.6Z"/>
                </svg>
                <span>{{ $isAr ? 'المتابعة باستخدام Google' : 'Continue with Google' }}</span>
            </a>
        </form>

        <div class="border-t border-outline-variant/10 px-6 py-5 text-center sm:px-8">
            <p class="text-sm text-on-surface-variant">
                {{ $isAr ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login', ['locale' => $locale, 'redirect_to' => $redirectTo]) }}" class="font-semibold text-primary-container hover:text-secondary-container">
                    {{ $isAr ? 'تسجيل الدخول' : 'Sign In' }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
