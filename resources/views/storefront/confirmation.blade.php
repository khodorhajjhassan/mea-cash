@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تم تأكيد الطلب - MeaCash' : 'Secure Order Confirmed - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="relative mx-auto max-w-[600px] px-4 py-16 text-center animate-fade-in">
    {{-- Decorative Background --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-[radial-gradient(circle_at_50%_20%,rgba(0,240,255,0.12),transparent_40%),radial-gradient(circle_at_20%_50%,rgba(254,0,254,0.06),transparent_35%)] blur-3xl"></div>

    <div class="glass-panel overflow-hidden rounded-[40px] border-outline-variant/10 p-8 md:p-12 shadow-[0_32px_64px_rgba(0,0,0,0.4)]">
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-primary-container shadow-[0_0_30px_rgba(0,240,255,0.4)]">
                    <span class="material-symbols-outlined text-5xl font-black text-background">check_circle</span>
                </div>
                <div class="absolute -inset-4 animate-pulse rounded-full border-2 border-primary-container/20"></div>
            </div>
        </div>

        <span class="mb-3 block font-label text-[10px] font-black uppercase tracking-[0.4em] text-primary-container">
            {{ $locale === 'ar' ? 'عملية دفع آمنة' : 'SECURE TRANSACTION SUCCESS' }}
        </span>
        
        <h1 class="mb-4 font-headline text-3xl font-black italic uppercase leading-none tracking-tighter text-on-surface md:text-5xl">
            {{ $locale === 'ar' ? 'تم تأكيد طلبك!' : 'Order Confirmed' }}
        </h1>

        <div class="mb-8 inline-flex items-center gap-3 rounded-full bg-surface-container-highest px-6 py-2 border border-outline-variant/30">
            <span class="font-label text-[10px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'رقم التتبع' : 'TRACKING ID' }}:</span>
            <span class="font-headline text-sm font-black text-primary-container">{{ $order->order_number }}</span>
        </div>

        <p class="mb-10 text-sm leading-relaxed text-on-surface-variant opacity-80">
            {{ $locale === 'ar' ? 'طلبك قيد المعالجة الآن. ستظهر بيانات التسليم والإيصالات في لوحة التحكم الخاصة بك فور اكتمال التشفير.' : 'Your order has been initiated in our secure vault. Delivery data and receipts will appear in your dashboard instantly after the processing is complete.' }}
        </p>

        <div class="space-y-4">
            <a href="{{ route('store.orders.detail', $order->order_number) }}" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container-dim px-8 py-5 font-headline text-xs font-black uppercase tracking-[0.25em] text-background transition hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                {{ $locale === 'ar' ? 'تتبع الإنجاز' : 'Track Fulfillment' }}
                <span class="material-symbols-outlined text-lg">monitoring</span>
            </a>
            
            <a href="{{ route('store.home') }}" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-8 py-4 font-label text-[10px] font-black uppercase tracking-widest text-on-surface transition hover:border-primary-container/40 hover:bg-surface-container-highest">
                {{ $locale === 'ar' ? 'متابعة التسوق' : 'Continue Shopping' }}
                <span class="material-symbols-outlined text-sm">shopping_bag</span>
            </a>
        </div>
    </div>

    {{-- Reassurance --}}
    <div class="mt-10 flex items-center justify-center gap-8 text-outline grayscale opacity-40">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">verified_user</span>
            <span class="font-label text-[9px] font-black uppercase tracking-widest">Secured</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">bolt</span>
            <span class="font-label text-[9px] font-black uppercase tracking-widest">Fast Delivery</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">support</span>
            <span class="font-label text-[9px] font-black uppercase tracking-widest">24/7 Intel</span>
        </div>
    </div>
</div>
@endsection
