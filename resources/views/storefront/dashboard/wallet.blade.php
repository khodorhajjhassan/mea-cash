@extends('storefront.layouts.app')
@section('title', app()->getLocale() == 'ar' ? 'محفظتي - MeaCash' : 'My Wallet - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold font-heading" style="color: var(--sf-text);">{{ $locale == 'ar' ? '💰 محفظتي' : '💰 My Wallet' }}</h1>
        <a href="{{ route('store.dashboard') }}" class="sf-btn-outline" style="height: 2rem; font-size: 0.75rem;">← {{ $locale == 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left: Balance + Top-up --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Balance Card --}}
            <div class="sf-panel p-5" style="border-radius: var(--sf-radius-lg);">
                <div class="sf-stat-card-label">{{ $locale == 'ar' ? 'الرصيد الحالي' : 'Current Balance' }}</div>
                <div class="mt-2 text-3xl font-bold" style="color: var(--sf-green);">${{ number_format($balance, 2) }}</div>
            </div>

            {{-- Top-up Form --}}
            <div class="sf-panel p-5" style="border-radius: var(--sf-radius-lg);">
                <h3 class="text-sm font-bold mb-4" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'شحن المحفظة' : 'Top Up Wallet' }}</h3>
                <form action="{{ route('store.wallet.topup') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="sf-field">
                        <label for="payment_method">{{ $locale == 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</label>
                        <select name="payment_method" id="payment_method" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->method }}">{{ $pm->{"display_name_{$locale}"} }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sf-field">
                        <label for="amount_requested">{{ $locale == 'ar' ? 'المبلغ ($)' : 'Amount ($)' }}</label>
                        <input type="number" name="amount_requested" id="amount_requested" min="1" max="10000" step="0.01" required placeholder="25.00">
                    </div>
                    <div class="sf-field">
                        <label for="receipt_image">{{ $locale == 'ar' ? 'صورة الإيصال' : 'Receipt Image' }}</label>
                        <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required class="text-sm" style="color: var(--sf-muted);">
                    </div>
                    <button type="submit" class="sf-btn-gold w-full">
                        {{ $locale == 'ar' ? 'إرسال طلب الشحن' : 'Submit Top-up Request' }}
                    </button>
                </form>
            </div>

            {{-- Pending Requests --}}
            @if($pendingTopups->isNotEmpty())
            <div class="sf-panel p-5" style="border-radius: var(--sf-radius-lg);">
                <h3 class="text-sm font-bold mb-3" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'طلبات شحن قيد الانتظار' : 'Pending Top-ups' }}</h3>
                @foreach($pendingTopups as $topup)
                <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--sf-border);">
                    <div>
                        <span class="text-sm font-medium" style="color: var(--sf-text);">${{ number_format($topup->amount_requested, 2) }}</span>
                        <span class="text-xs ml-2" style="color: var(--sf-muted);">{{ $topup->payment_method }}</span>
                    </div>
                    <span class="sf-pill sf-pill-pending">{{ $locale == 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Transaction History --}}
        <div class="lg:col-span-2">
            <div class="sf-panel p-5" style="border-radius: var(--sf-radius-lg);">
                <h3 class="text-sm font-bold mb-4" style="color: var(--sf-text);">{{ $locale == 'ar' ? 'سجل المعاملات' : 'Transaction History' }}</h3>
                @if(method_exists($transactions, 'isNotEmpty') ? $transactions->isNotEmpty() : $transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="sf-table">
                        <thead>
                            <tr>
                                <th>{{ $locale == 'ar' ? 'النوع' : 'Type' }}</th>
                                <th>{{ $locale == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ $locale == 'ar' ? 'الرصيد بعد' : 'Balance After' }}</th>
                                <th>{{ $locale == 'ar' ? 'الوصف' : 'Description' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                            <tr>
                                <td><span class="sf-pill sf-pill-{{ in_array($tx->type->value ?? $tx->type, ['topup', 'refund']) ? 'completed' : 'pending' }}">{{ ucfirst($tx->type->value ?? $tx->type) }}</span></td>
                                <td class="font-bold" style="color: {{ in_array($tx->type->value ?? $tx->type, ['topup', 'refund']) ? 'var(--sf-green)' : 'var(--sf-hot-red)' }};">
                                    {{ in_array($tx->type->value ?? $tx->type, ['topup', 'refund']) ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                </td>
                                <td>${{ number_format($tx->balance_after, 2) }}</td>
                                <td class="text-xs" style="color: var(--sf-muted);">{{ $tx->{"description_{$locale}"} ?? $tx->description_en }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($transactions, 'links'))
                <div class="mt-4 flex justify-center">{{ $transactions->links() }}</div>
                @endif
                @else
                <p class="text-sm text-center py-6" style="color: var(--sf-muted);">{{ $locale == 'ar' ? 'لا توجد معاملات' : 'No transactions yet' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
