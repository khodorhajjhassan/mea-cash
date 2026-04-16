@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'حسابي - MeaCash' : 'Profile - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6 max-w-lg mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold font-heading" style="color: var(--sf-text);">{{ $locale == 'ar' ? '👤 حسابي' : '👤 My Profile' }}</h1>
        <a href="{{ route('store.dashboard') }}" class="sf-btn-outline" style="height: 2rem; font-size: 0.75rem;">← {{ $locale == 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</a>
    </div>

    <div class="sf-panel p-5 sm:p-8" style="border-radius: var(--sf-radius-xl);">
        <form action="{{ route('store.profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div class="sf-field">
                <label for="name">{{ $locale == 'ar' ? 'الاسم' : 'Name' }}</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <p class="text-xs mt-1" style="color: var(--sf-hot-red);">{{ $message }}</p> @enderror
            </div>

            <div class="sf-field">
                <label for="email">{{ $locale == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" id="email" value="{{ $user->email }}" disabled style="opacity: 0.5;">
                <p class="text-xs" style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'لا يمكن تغيير البريد الإلكتروني' : 'Email cannot be changed' }}</p>
            </div>

            <div class="sf-field">
                <label for="phone">{{ $locale == 'ar' ? 'رقم الهاتف' : 'Phone' }}</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+961...">
            </div>

            <div class="sf-field">
                <label for="preferred_language">{{ $locale == 'ar' ? 'اللغة المفضلة' : 'Preferred Language' }}</label>
                <select name="preferred_language" id="preferred_language">
                    <option value="en" {{ $user->preferred_language == 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ $user->preferred_language == 'ar' ? 'selected' : '' }}>العربية</option>
                </select>
            </div>

            <button type="submit" class="sf-btn-gold w-full">
                {{ $locale == 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
            </button>
        </form>

        <hr class="sf-divider">

        <form action="{{ route('store.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sf-btn-outline w-full" style="color: var(--sf-hot-red); border-color: rgba(255,75,75,0.3);">
                {{ $locale == 'ar' ? 'تسجيل الخروج' : 'Sign Out' }}
            </button>
        </form>
    </div>
</div>
@endsection
