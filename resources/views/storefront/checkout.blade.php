@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'إتمام الشراء - MeaCash' : 'Checkout - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold font-heading mb-6" style="color: var(--sf-text);">
        {{ $locale == 'ar' ? '💳 إتمام الشراء' : '💳 Checkout' }}
    </h1>

    {{-- Wallet Balance --}}
    <div class="sf-wallet-balance mb-6">
        <div>
            <div class="text-xs font-medium uppercase tracking-wider" style="color: var(--sf-muted);">
                {{ $locale == 'ar' ? 'رصيد المحفظة' : 'Wallet Balance' }}
            </div>
            <div class="sf-wallet-amount">${{ number_format($balance, 2) }}</div>
        </div>
        @if(!$hasSufficientBalance)
            <div class="flex-1 text-right">
                <span class="text-xs font-semibold" style="color: var(--sf-hot-red);">
                    {{ $locale == 'ar' ? '⚠️ الرصيد غير كافٍ' : '⚠️ Insufficient balance' }}
                </span>
                <a href="{{ route('store.wallet') }}" class="block text-xs mt-1 underline" style="color: var(--sf-gold-light);">
                    {{ $locale == 'ar' ? 'شحن المحفظة' : 'Top up wallet' }}
                </a>
            </div>
        @endif
    </div>

    {{-- Order Summary --}}
    <div class="sf-panel p-5 mb-6" style="border-radius: var(--sf-radius-lg);">
        <h3 class="text-sm font-semibold mb-4" style="color: var(--sf-text);">
            {{ $locale == 'ar' ? 'ملخص الطلب' : 'Order Summary' }}
        </h3>
        <div class="space-y-3">
            @foreach($items as $item)
            <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--sf-border);">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color: var(--sf-text);">{{ $item['product_name'] }}</p>
                    @if($item['package_name'])
                        <p class="text-xs" style="color: var(--sf-muted);">{{ $item['package_name'] }} × {{ $item['quantity'] }}</p>
                    @endif
                </div>
                <span class="text-sm font-bold" style="color: var(--sf-gold-light);">
                    ${{ number_format($item['unit_price'] * $item['quantity'], 2) }}
                </span>
            </div>
            @endforeach
        </div>

        <div class="sf-cart-total mt-2">
            <span class="font-bold">{{ $locale == 'ar' ? 'المجموع' : 'Total' }}</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>
    </div>

    {{-- Place Order --}}
    @if($hasSufficientBalance)
    <form action="{{ route('store.checkout.process') }}" method="POST">
        @csrf
        <button type="submit" class="sf-btn-gold w-full" style="height: 3.5rem; font-size: 1rem;">
            {{ $locale == 'ar' ? '✅ تأكيد الشراء (' . '$' . number_format($total, 2) . ')' : '✅ Confirm Purchase ($' . number_format($total, 2) . ')' }}
        </button>
        <p class="mt-3 text-xs text-center" style="color: var(--sf-muted);">
            {{ $locale == 'ar' ? 'سيتم خصم المبلغ من رصيد محفظتك فوراً.' : 'The amount will be deducted from your wallet immediately.' }}
        </p>
    </form>
    @else
    <a href="{{ route('store.wallet') }}" class="sf-btn-gold w-full text-center" style="height: 3.5rem; font-size: 1rem;">
        {{ $locale == 'ar' ? 'شحن المحفظة للمتابعة' : 'Top Up Wallet to Continue' }}
    </a>
    @endif
</div>
@endsection
