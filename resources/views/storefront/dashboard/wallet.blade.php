@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'محفظتي - MeaCash' : 'My Wallet - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $selectedMethod = old('payment_method', $paymentMethods->first()?->method);
    $methodStyles = [
        'omt' => ['icon' => 'payments', 'from' => '#00f0ff', 'to' => '#7df4ff'],
        'wish' => ['icon' => 'account_balance', 'from' => '#fe00fe', 'to' => '#ffabf3'],
        'usdt' => ['icon' => 'currency_bitcoin', 'from' => '#fbbf24', 'to' => '#fde68a'],
    ];
@endphp

<div class="relative mx-auto max-w-[1440px] px-4 py-10 md:px-8">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_15%_20%,rgba(0,240,255,0.16),transparent_30%),radial-gradient(circle_at_85%_8%,rgba(251,191,36,0.13),transparent_28%)] blur-3xl"></div>

    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="font-label text-[10px] font-black uppercase tracking-[0.28em] text-primary-container">
                {{ $locale === 'ar' ? 'إدارة الرصيد' : 'Balance Control' }}
            </p>
            <h1 class="mt-2 font-headline text-4xl font-black uppercase tracking-tight text-on-surface md:text-5xl">
                {{ $locale === 'ar' ? 'محفظتي' : 'Wallet' }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-on-surface-variant">
                {{ $locale === 'ar' ? 'اختر طريقة الدفع، أرسل الإيصال، وتابع طلبات الشحن من نفس الصفحة.' : 'Choose a payment method, upload your receipt, and track top-up requests from one place.' }}
            </p>
        </div>

        <a href="{{ route('store.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-5 py-3 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant transition hover:border-primary-container/50 hover:text-primary-container">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>{{ $locale === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.45fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] border border-primary-container/20 bg-surface-container-low/80 p-6 shadow-2xl shadow-black/20">
                <div class="font-label text-[10px] font-black uppercase tracking-[0.24em] text-outline">
                    {{ $locale === 'ar' ? 'الرصيد الحالي' : 'Current Balance' }}
                </div>
                <div class="mt-3 font-headline text-5xl font-black tracking-tight text-primary-container">${{ number_format($balance, 2) }}</div>
                <p class="mt-3 text-sm text-on-surface-variant">{{ $locale === 'ar' ? 'يتم استخدام الرصيد مباشرة عند الشراء.' : 'Your wallet is used instantly during checkout.' }}</p>
            </div>

            <div class="rounded-[2rem] border border-outline-variant/15 bg-surface-container/80 p-5 shadow-2xl shadow-black/20 md:p-6">
                <h2 class="mb-5 font-headline text-xl font-black uppercase text-on-surface">
                    {{ $locale === 'ar' ? 'شحن المحفظة' : 'Top Up Wallet' }}
                </h2>

                @if($paymentMethods->isEmpty())
                    <div class="rounded-2xl border border-error/20 bg-error-container/10 p-5 text-sm text-error">
                        {{ $locale === 'ar' ? 'لا توجد طرق دفع متاحة حالياً.' : 'No payment methods are available right now.' }}
                    </div>
                @else
                    <form id="topup-form" action="{{ route('store.wallet.topup') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <label class="mb-3 block font-label text-[10px] font-black uppercase tracking-widest text-outline">
                                {{ $locale === 'ar' ? 'طريقة الدفع' : 'Payment Method' }}
                            </label>
                            <div class="grid gap-3 sm:grid-cols-3">
                                @foreach($paymentMethods as $pm)
                                    @php
                                        $style = $methodStyles[$pm->method] ?? ['icon' => 'credit_card', 'from' => '#00f0ff', 'to' => '#fe00fe'];
                                        $name = $pm->{"display_name_{$locale}"} ?: $pm->display_name_en;
                                        $instructions = $pm->{"instructions_{$locale}"} ?: $pm->instructions_en;
                                    @endphp
                                    <label class="group relative cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $pm->method }}" class="peer sr-only" required @checked($selectedMethod === $pm->method)>
                                        <div class="min-h-[132px] rounded-2xl border border-outline-variant/15 bg-surface-container-low p-4 transition-all duration-300 peer-checked:scale-[1.02] peer-checked:border-primary-container peer-checked:shadow-[0_0_28px_rgba(0,240,255,0.16)] group-hover:border-primary-container/45">
                                            <div class="mb-4 flex items-center justify-between">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-xl text-on-primary-fixed shadow-lg" style="background: linear-gradient(135deg, {{ $style['from'] }}, {{ $style['to'] }});">
                                                    <span class="material-symbols-outlined text-xl">{{ $style['icon'] }}</span>
                                                </div>
                                                <span class="material-symbols-outlined hidden text-primary-container peer-checked:block">check_circle</span>
                                            </div>
                                            <div class="font-headline text-sm font-black uppercase text-on-surface">{{ $name }}</div>
                                            <div class="mt-1 truncate font-label text-[9px] uppercase tracking-widest text-outline">{{ $pm->account_identifier }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div id="payment-method-details" class="rounded-2xl border border-outline-variant/15 bg-surface-container-lowest/40 p-4">
                            @foreach($paymentMethods as $pm)
                                @php
                                    $name = $pm->{"display_name_{$locale}"} ?: $pm->display_name_en;
                                    $instructions = $pm->{"instructions_{$locale}"} ?: $pm->instructions_en;
                                @endphp
                                <div class="{{ $selectedMethod === $pm->method ? '' : 'hidden' }}" data-payment-detail="{{ $pm->method }}">
                                    <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'أرسل إلى' : 'Send To' }}</div>
                                    <div class="mt-1 font-headline text-lg font-black text-primary-container">{{ $pm->account_identifier }}</div>
                                    @if($instructions)
                                        <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">{{ $instructions }}</p>
                                    @else
                                        <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">{{ $locale === 'ar' ? 'ارفع صورة الإيصال بعد إتمام التحويل.' : 'Upload your receipt after completing the transfer.' }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="sf-field">
                                <label for="amount_requested">{{ $locale === 'ar' ? 'المبلغ بالدولار' : 'Amount in USD' }}</label>
                                <input type="number" name="amount_requested" id="amount_requested" min="1" max="10000" step="0.01" required value="{{ old('amount_requested') }}" placeholder="25.00">
                                @error('amount_requested') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="sf-field">
                                <label for="receipt_image">{{ $locale === 'ar' ? 'صورة الإيصال' : 'Receipt Image' }}</label>
                                <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required class="text-sm text-on-surface-variant">
                                @error('receipt_image') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" id="topup-submit-btn" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-fixed to-secondary-fixed-dim px-5 py-4 font-headline text-sm font-black uppercase tracking-[0.2em] text-on-primary-fixed transition hover:scale-[1.01] active:scale-[0.99] disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="btn-text">{{ $locale === 'ar' ? 'إرسال طلب الشحن' : 'Submit Top-Up Request' }}</span>
                            <span id="btn-icon" class="material-symbols-outlined text-lg">bolt</span>
                        </button>
                    </form>
                @endif
            </div>

            @if($pendingTopups->isNotEmpty())
                <div class="rounded-[2rem] border border-outline-variant/15 bg-surface-container-low/80 p-5">
                    <h3 class="mb-3 font-headline text-sm font-black uppercase text-on-surface">{{ $locale === 'ar' ? 'طلبات قيد الانتظار' : 'Pending Top-Ups' }}</h3>
                    <div class="space-y-2">
                        @foreach($pendingTopups as $topup)
                            <div class="flex items-center justify-between rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-3">
                                <div>
                                    <div class="font-headline text-sm font-black text-on-surface">${{ number_format($topup->amount_requested, 2) }}</div>
                                    <div class="font-label text-[9px] uppercase tracking-widest text-outline">{{ strtoupper($topup->payment_method) }}</div>
                                </div>
                                <span class="rounded-full border border-yellow-500/20 bg-yellow-500/10 px-3 py-1 font-label text-[9px] font-black uppercase tracking-widest text-yellow-500">{{ $locale === 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-[2rem] border border-outline-variant/15 bg-surface-container/80 p-5 shadow-2xl shadow-black/20 md:p-6">
            <h2 class="mb-5 font-headline text-xl font-black uppercase text-on-surface">{{ $locale === 'ar' ? 'سجل المحفظة' : 'Wallet History' }}</h2>

            @if(method_exists($transactions, 'isNotEmpty') ? $transactions->isNotEmpty() : $transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="sf-table">
                        <thead>
                            <tr>
                                <th>{{ $locale === 'ar' ? 'النوع' : 'Type' }}</th>
                                <th>{{ $locale === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ $locale === 'ar' ? 'الرصيد بعد' : 'Balance After' }}</th>
                                <th>{{ $locale === 'ar' ? 'الوصف' : 'Description' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                @php
                                    $type = $tx->type->value ?? $tx->type;
                                    $isCredit = in_array($type, ['topup', 'refund'], true);
                                @endphp
                                <tr>
                                    <td><span class="sf-pill sf-pill-{{ $isCredit ? 'completed' : 'pending' }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</span></td>
                                    <td class="font-bold" style="color: {{ $isCredit ? 'var(--sf-green)' : 'var(--sf-hot-red)' }};">
                                        {{ $isCredit ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td>${{ number_format($tx->balance_after, 2) }}</td>
                                    <td class="text-xs text-on-surface-variant">{{ $tx->{"description_{$locale}"} ?? $tx->description_en }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($transactions, 'links'))
                    <div class="mt-4 flex justify-center">{{ $transactions->links() }}</div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center rounded-3xl border border-outline-variant/10 bg-surface-container-lowest/30 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline/30">history</span>
                    <p class="mt-4 font-label text-[10px] font-black uppercase tracking-[0.24em] text-outline">{{ $locale === 'ar' ? 'لا توجد معاملات بعد' : 'No transactions yet' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const details = document.querySelectorAll('[data-payment-detail]');

        const syncDetails = () => {
            const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
            details.forEach((detail) => {
                detail.classList.toggle('hidden', detail.dataset.paymentDetail !== selected);
            });
        };

        radios.forEach((radio) => radio.addEventListener('change', syncDetails));
        syncDetails();

        // Handle form loading state
        const form = document.getElementById('topup-form');
        const submitBtn = document.getElementById('topup-submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');

        if (form) {
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnText.textContent = '{{ $locale === "ar" ? "جاري الإرسال..." : "Processing..." }}';
                btnIcon.textContent = 'refresh';
                btnIcon.classList.add('animate-spin');
            });
        }
    });
</script>
@endpush
@endsection
