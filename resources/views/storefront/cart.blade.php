@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'السلة - MeaCash' : 'Cart - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold font-heading mb-6" style="color: var(--sf-text);">
        {{ $locale == 'ar' ? '🛒 سلة التسوق' : '🛒 Shopping Cart' }}
    </h1>

    @if(count($items) > 0)
        <div class="space-y-3 mb-6">
            @foreach($items as $item)
            <div class="sf-cart-item">
                <div class="sf-cart-item-img">
                    @if($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl opacity-40">🎮</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold truncate" style="color: var(--sf-text);">{{ $item['product_name'] }}</h3>
                    @if($item['package_name'])
                        <p class="text-xs mt-0.5" style="color: var(--sf-muted);">{{ $item['package_name'] }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold" style="color: var(--sf-gold-light);">
                            ${{ number_format($item['unit_price'] * $item['quantity'], 2) }}
                        </span>
                        <span class="text-xs" style="color: var(--sf-muted);">x{{ $item['quantity'] }}</span>
                    </div>
                </div>
                <form action="{{ route('store.cart.remove', $item['id']) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg transition-colors" style="color: var(--sf-hot-red);" aria-label="Remove">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="sf-cart-total">
            <span>{{ $locale == 'ar' ? 'المجموع' : 'Total' }}</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('store.checkout') }}" class="sf-btn-gold w-full sm:flex-1 text-center">
                {{ $locale == 'ar' ? 'إتمام الشراء' : 'Proceed to Checkout' }}
            </a>
            <form action="{{ route('store.cart.clear') }}" method="POST" class="sm:flex-none">
                @csrf @method('DELETE')
                <button type="submit" class="sf-btn-outline w-full">
                    {{ $locale == 'ar' ? 'تفريغ السلة' : 'Clear Cart' }}
                </button>
            </form>
        </div>
    @else
        <div class="sf-panel p-8 text-center" style="border-radius: var(--sf-radius-lg);">
            <div class="text-4xl mb-4 opacity-40">🛒</div>
            <p class="text-base font-semibold" style="color: var(--sf-text);">
                {{ $locale == 'ar' ? 'سلتك فارغة' : 'Your cart is empty' }}
            </p>
            <p class="mt-2 text-sm" style="color: var(--sf-muted);">
                {{ $locale == 'ar' ? 'أضف بعض المنتجات للبدء.' : 'Add some products to get started.' }}
            </p>
            <a href="{{ route('store.home') }}" class="sf-btn-gold mt-6 inline-flex">
                {{ $locale == 'ar' ? 'تصفح المنتجات' : 'Browse Products' }}
            </a>
        </div>
    @endif
</div>
@endsection
