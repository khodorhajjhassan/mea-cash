@extends('storefront.layouts.app')
@section('title', ($locale ?? 'en') == 'ar' ? 'تفاصيل الطلب - MeaCash' : 'Order Detail - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6 max-w-2xl mx-auto">
    <a href="{{ route('store.orders') }}" class="text-xs font-medium mb-4 inline-block" style="color: var(--sf-gold-light);">← {{ $locale == 'ar' ? 'العودة للطلبات' : 'Back to Orders' }}</a>

    <div class="sf-panel p-5 sm:p-8" style="border-radius: var(--sf-radius-xl);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold font-heading" style="color: var(--sf-text);">{{ $order->order_number }}</h1>
                <p class="text-xs mt-1" style="color: var(--sf-muted);">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <span class="sf-pill sf-pill-{{ $order->status->value }}">{{ ucfirst($order->status->value) }}</span>
        </div>

        <hr class="sf-divider">

        {{-- Product Info --}}
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'المنتج' : 'Product' }}</span>
                <span style="color: var(--sf-text);">{{ $order->product?->{"name_{$locale}"} }}</span>
            </div>
            @if($order->package)
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'الباقة' : 'Package' }}</span>
                <span style="color: var(--sf-text);">{{ $order->package->{"name_{$locale}"} }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'الكمية' : 'Quantity' }}</span>
                <span style="color: var(--sf-text);">{{ $order->quantity }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</span>
                <span style="color: var(--sf-text);">${{ number_format($order->unit_price, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold">
                <span style="color: var(--sf-text);">{{ $locale == 'ar' ? 'المجموع' : 'Total' }}</span>
                <span style="color: var(--sf-gold-light);">${{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        {{-- Fulfillment Data (delivered items) --}}
        @if($order->items->isNotEmpty() && in_array($order->status->value, ['completed']))
        <hr class="sf-divider">
        <h3 class="text-sm font-bold mb-3" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'بيانات التسليم' : 'Delivery Details' }}</h3>
        <div class="space-y-2">
            @foreach($order->items as $item)
            <div class="p-3 rounded-xl" style="border: 1px solid var(--sf-border); background: rgba(255,255,255,0.03);">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase" style="color: var(--sf-muted);">{{ ucfirst($item->type ?? 'code') }}</span>
                    <button onclick="this.previousElementSibling.textContent = this.dataset.val; this.remove();"
                        data-val="{{ $item->delivered_value }}"
                        class="text-xs font-semibold px-2 py-1 rounded-lg" style="color: var(--sf-gold-light); border: 1px solid rgba(216,154,29,0.3);">
                        {{ $locale == 'ar' ? 'كشف' : 'Reveal' }}
                    </button>
                </div>
                <p class="mt-2 text-sm font-mono break-all" style="color: var(--sf-text);">••••••••••</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Delivery Notes --}}
        @if($order->delivery_notes)
        <hr class="sf-divider">
        <h3 class="text-sm font-bold mb-2" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'ملاحظات التسليم' : 'Delivery Notes' }}</h3>
        <p class="text-sm" style="color: var(--sf-muted);">{!! nl2br(e($order->delivery_notes)) !!}</p>
        @endif

        {{-- Refund Notes --}}
        @if($order->refund_notes)
        <hr class="sf-divider">
        <div class="sf-alert sf-alert-error">
            <strong>{{ $locale == 'ar' ? 'ملاحظات الاسترداد:' : 'Refund Notes:' }}</strong> {{ $order->refund_notes }}
        </div>
        @endif
    </div>
</div>
@endsection
