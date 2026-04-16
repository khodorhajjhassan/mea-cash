@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'لوحة التحكم - MeaCash' : 'Dashboard - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6">
    <h1 class="text-2xl font-bold font-heading mb-6" style="color: var(--sf-text);">
        {{ $locale == 'ar' ? 'مرحباً، ' . auth()->user()->name : 'Welcome, ' . auth()->user()->name }}
    </h1>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
        <div class="sf-stat-card">
            <div class="sf-stat-card-label">{{ $locale == 'ar' ? 'رصيد المحفظة' : 'Wallet Balance' }}</div>
            <div class="sf-stat-card-value" style="color: var(--sf-green);">${{ number_format($balance, 2) }}</div>
        </div>
        <div class="sf-stat-card">
            <div class="sf-stat-card-label">{{ $locale == 'ar' ? 'إجمالي الطلبات' : 'Total Orders' }}</div>
            <div class="sf-stat-card-value">{{ $totalOrders }}</div>
        </div>
        <div class="sf-stat-card">
            <div class="sf-stat-card-label">{{ $locale == 'ar' ? 'إجمالي الإنفاق' : 'Total Spent' }}</div>
            <div class="sf-stat-card-value" style="color: var(--sf-gold-light);">${{ number_format($totalSpent, 2) }}</div>
        </div>
        <div class="sf-stat-card">
            <a href="{{ route('store.wallet') }}" class="block">
                <div class="sf-stat-card-label">{{ $locale == 'ar' ? 'شحن المحفظة' : 'Top Up' }}</div>
                <div class="sf-stat-card-value text-base" style="color: var(--sf-gold);">→ {{ $locale == 'ar' ? 'شحن الآن' : 'Top Up Now' }}</div>
            </a>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <a href="{{ route('store.orders') }}" class="sf-panel p-4 text-center transition-all hover:scale-[1.02]" style="border-radius: var(--sf-radius-md);">
            <div class="text-2xl mb-2">📦</div>
            <span class="text-sm font-medium" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'طلباتي' : 'My Orders' }}</span>
        </a>
        <a href="{{ route('store.wallet') }}" class="sf-panel p-4 text-center transition-all hover:scale-[1.02]" style="border-radius: var(--sf-radius-md);">
            <div class="text-2xl mb-2">💰</div>
            <span class="text-sm font-medium" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'محفظتي' : 'My Wallet' }}</span>
        </a>
        <a href="{{ route('store.profile') }}" class="sf-panel p-4 text-center transition-all hover:scale-[1.02]" style="border-radius: var(--sf-radius-md);">
            <div class="text-2xl mb-2">👤</div>
            <span class="text-sm font-medium" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'حسابي' : 'Profile' }}</span>
        </a>
        <a href="{{ route('store.home') }}" class="sf-panel p-4 text-center transition-all hover:scale-[1.02]" style="border-radius: var(--sf-radius-md);">
            <div class="text-2xl mb-2">🛒</div>
            <span class="text-sm font-medium" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'تسوق' : 'Shop' }}</span>
        </a>
    </div>

    {{-- Recent Orders --}}
    <div class="sf-panel p-5" style="border-radius: var(--sf-radius-lg);">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold font-heading" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'آخر الطلبات' : 'Recent Orders' }}</h2>
            <a href="{{ route('store.orders') }}" class="text-xs font-semibold" style="color: var(--sf-gold-light);">{{ $locale == 'ar' ? 'عرض الكل' : 'View All' }}</a>
        </div>

        @if($recentOrders->isNotEmpty())
        <div class="space-y-3">
            @foreach($recentOrders as $order)
            <a href="{{ route('store.orders.detail', $order->order_number) }}" class="flex items-center justify-between p-3 rounded-xl transition-colors" style="border: 1px solid var(--sf-border); background: rgba(255,255,255,0.02);">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color: var(--sf-text);">{{ $order->product?->{"name_{$locale}"} }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--sf-muted);">{{ $order->order_number }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right ml-4">
                    <span class="text-sm font-bold" style="color: var(--sf-gold-light);">${{ number_format($order->total_price, 2) }}</span>
                    <div class="mt-1">
                        <span class="sf-pill sf-pill-{{ $order->status->value }}">{{ ucfirst($order->status->value) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-sm text-center py-6" style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'لا توجد طلبات بعد.' : 'No orders yet.' }}</p>
        @endif
    </div>
</div>
@endsection
