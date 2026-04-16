@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'طلباتي - MeaCash' : 'My Orders - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold font-heading" style="color: var(--sf-text);">{{ $locale == 'ar' ? '📦 طلباتي' : '📦 My Orders' }}</h1>
        <a href="{{ route('store.dashboard') }}" class="sf-btn-outline" style="height: 2rem; font-size: 0.75rem;">← {{ $locale == 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</a>
    </div>

    @if($orders->isNotEmpty())
    <div class="sf-panel overflow-hidden" style="border-radius: var(--sf-radius-lg);">
        <div class="overflow-x-auto">
            <table class="sf-table">
                <thead>
                    <tr>
                        <th>{{ $locale == 'ar' ? 'رقم الطلب' : 'Order #' }}</th>
                        <th>{{ $locale == 'ar' ? 'المنتج' : 'Product' }}</th>
                        <th>{{ $locale == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ $locale == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ $locale == 'ar' ? 'التاريخ' : 'Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('store.orders.detail', $order->order_number) }}" class="font-medium hover:underline" style="color: var(--sf-gold-light);">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->product?->{"name_{$locale}"} ?? '-' }}</td>
                        <td class="font-bold" style="color: var(--sf-gold-light);">${{ number_format($order->total_price, 2) }}</td>
                        <td><span class="sf-pill sf-pill-{{ $order->status->value }}">{{ ucfirst($order->status->value) }}</span></td>
                        <td style="color: var(--sf-muted); font-size: 0.75rem;">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6 flex justify-center">{{ $orders->links() }}</div>
    @else
    <div class="sf-panel p-8 text-center" style="border-radius: var(--sf-radius-lg);">
        <div class="text-4xl mb-4 opacity-40">📦</div>
        <p class="font-semibold" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'لا توجد طلبات' : 'No orders yet' }}</p>
        <a href="{{ route('store.home') }}" class="sf-btn-gold mt-4 inline-flex">{{ $locale == 'ar' ? 'تسوق الآن' : 'Shop Now' }}</a>
    </div>
    @endif
</div>
@endsection
