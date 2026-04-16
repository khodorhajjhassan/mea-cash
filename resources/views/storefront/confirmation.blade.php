@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'تم الطلب بنجاح - MeaCash' : 'Order Confirmed - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-12 max-w-lg mx-auto text-center">
    <div class="sf-panel p-8" style="border-radius: var(--sf-radius-xl);">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-2xl font-bold font-heading" style="color: var(--sf-text);">
            {{ $locale == 'ar' ? 'تم تأكيد طلبك!' : 'Order Confirmed!' }}
        </h1>
        <p class="mt-2 text-sm" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'رقم الطلب' : 'Order Number' }}:
            <strong style="color: var(--sf-gold-light);">{{ $order->order_number }}</strong>
        </p>
        <p class="mt-4 text-sm" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'سيتم معالجة طلبك قريباً. ستتلقى تفاصيل التسليم بعد الإكمال.' : 'Your order is being processed. You will receive delivery details once completed.' }}
        </p>

        <hr class="sf-divider">

        <div class="text-left space-y-2">
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'المنتج' : 'Product' }}</span>
                <span style="color: var(--sf-text);">{{ $order->product->{"name_{$locale}"} }}</span>
            </div>
            @if($order->package)
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'الباقة' : 'Package' }}</span>
                <span style="color: var(--sf-text);">{{ $order->package->{"name_{$locale}"} }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'المبلغ' : 'Amount' }}</span>
                <span class="font-bold" style="color: var(--sf-gold-light);">${{ number_format($order->total_price, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'الحالة' : 'Status' }}</span>
                <span class="sf-pill sf-pill-pending">{{ ucfirst($order->status->value) }}</span>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('store.orders.detail', $order->order_number) }}" class="sf-btn-gold w-full">
                {{ $locale == 'ar' ? 'تتبع الطلب' : 'Track Order' }}
            </a>
            <a href="{{ route('store.home') }}" class="sf-btn-outline w-full">
                {{ $locale == 'ar' ? 'متابعة التسوق' : 'Continue Shopping' }}
            </a>
        </div>
    </div>
</div>
@endsection
