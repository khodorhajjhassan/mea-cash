@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'محفظتي - MeaCash' : 'My Wallet - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $selectedMethod = old('payment_method', $paymentMethods->first()?->method);
    $methodStyles = [
        'omt' => ['icon' => 'payments', 'from' => '#00f0ff', 'to' => '#7df4ff'],
        'wish' => ['icon' => 'account_balance', 'from' => '#fe00fe', 'to' => '#ffabf3'],
        'usdt' => ['icon' => 'currency_bitcoin', 'from' => '#fbbf24', 'to' => '#fde68a'],
    ];
@endphp

<div class="relative mx-auto max-w-[1440px] px-4 py-8 md:px-8 md:py-10">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_15%_20%,rgba(0,240,255,0.16),transparent_30%),radial-gradient(circle_at_85%_8%,rgba(251,191,36,0.13),transparent_28%)] blur-3xl"></div>

    @if(request('notice') === 'insufficient-balance')
        <div class="mb-6 rounded-2xl border border-error/20 bg-error-container/10 px-5 py-4 text-sm text-error">
            {{ $locale === 'ar' ? 'لا يوجد رصيد كافٍ في محفظتك. يرجى شحن المحفظة أولاً ثم إعادة المحاولة.' : 'You do not have enough balance in your wallet. Please recharge your wallet first and try again.' }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 md:mb-8 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="font-label {{ $isArabic ? 'text-xs tracking-[0.08em]' : 'text-[10px] uppercase tracking-[0.28em]' }} font-black text-primary-container">
                {{ $locale === 'ar' ? 'إدارة الرصيد' : 'Balance Control' }}
            </p>
            <h1 class="mt-2 font-headline text-3xl font-black uppercase tracking-tight text-on-surface sm:text-4xl md:text-5xl">
                {{ $locale === 'ar' ? 'محفظتي' : 'Wallet' }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-on-surface-variant">
                {{ $locale === 'ar' ? 'اختر طريقة الدفع، ارفع الإيصال، وتابع طلبات الشحن من نفس الصفحة.' : 'Choose a payment method, upload your receipt, and track top-up requests from one place.' }}
            </p>
        </div>

        <a href="{{ route('store.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-4 py-3 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant transition hover:border-primary-container/50 hover:text-primary-container md:px-5">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>{{ $locale === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.45fr]">
        <div class="space-y-5 md:space-y-6">
            <div class="rounded-[1.5rem] border border-primary-container/20 bg-surface-container-low/80 p-5 shadow-2xl shadow-black/20 md:rounded-[2rem] md:p-6">
                <div class="font-label {{ $isArabic ? 'text-sm tracking-[0.05em]' : 'text-[10px] uppercase tracking-[0.24em]' }} font-black text-outline">
                    {{ $locale === 'ar' ? 'الرصيد الحالي' : 'Current Balance' }}
                </div>
                <div class="mt-3 break-words font-headline text-3xl font-black tracking-tight text-primary-container sm:text-5xl">${{ number_format($balance, 2) }}</div>
                <p class="mt-3 text-sm text-on-surface-variant">{{ $locale === 'ar' ? 'يتم استخدام الرصيد مباشرة عند الشراء.' : 'Your wallet is used instantly during checkout.' }}</p>
            </div>

            <div class="rounded-[1.5rem] border border-outline-variant/15 bg-surface-container/80 p-4 shadow-2xl shadow-black/20 md:rounded-[2rem] md:p-6">
                <h2 class="mb-4 font-headline text-lg font-black uppercase text-on-surface md:mb-5 md:text-xl">
                    {{ $locale === 'ar' ? 'شحن المحفظة' : 'Top Up Wallet' }}
                </h2>

                @if($paymentMethods->isEmpty())
                    <div class="rounded-2xl border border-error/20 bg-error-container/10 p-5 text-sm text-error">
                        {{ $locale === 'ar' ? 'لا توجد طرق دفع متاحة حاليا.' : 'No payment methods are available right now.' }}
                    </div>
                @else
                    <form id="topup-form" action="{{ route('store.wallet.topup') }}" method="POST" enctype="multipart/form-data" class="space-y-5" novalidate>
                        @csrf

                        <div id="topup-response" class="hidden rounded-2xl border px-4 py-3 text-sm"></div>

                        <div>
                            <label class="mb-3 block font-label {{ $isArabic ? 'text-sm tracking-normal' : 'text-[10px] uppercase tracking-widest' }} font-black text-outline">
                                {{ $locale === 'ar' ? 'طريقة الدفع' : 'Payment Method' }}
                            </label>
                            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                @foreach($paymentMethods as $pm)
                                    @php
                                        $style = $methodStyles[$pm->method] ?? ['icon' => 'credit_card', 'from' => '#00f0ff', 'to' => '#fe00fe'];
                                        $name = $pm->{"display_name_{$locale}"} ?: $pm->display_name_en;
                                    @endphp
                                    <label class="group relative cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $pm->method }}" class="peer sr-only" required @checked($selectedMethod === $pm->method)>
                                        <div class="min-h-[88px] rounded-2xl border border-outline-variant/15 bg-surface-container-low p-3 transition-all duration-300 peer-checked:scale-[1.02] peer-checked:border-primary-container peer-checked:shadow-[0_0_28px_rgba(0,240,255,0.16)] group-hover:border-primary-container/45 sm:min-h-[132px] sm:p-4">
                                            <div class="mb-3 flex items-center justify-between sm:mb-4">
                                                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-on-primary-fixed shadow-lg sm:h-11 sm:w-11" style="background: linear-gradient(135deg, {{ $style['from'] }}, {{ $style['to'] }});">
                                                    <span class="material-symbols-outlined text-lg sm:text-xl">{{ $style['icon'] }}</span>
                                                </div>
                                                <span class="material-symbols-outlined hidden text-lg text-primary-container peer-checked:block sm:text-2xl">check_circle</span>
                                            </div>
                                            <div class="truncate font-headline text-[11px] font-black uppercase text-on-surface sm:text-sm">{{ $name }}</div>
                                            <div class="mt-1 hidden truncate font-label text-[9px] uppercase tracking-widest text-outline sm:block">{{ $pm->account_identifier }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-2 hidden text-xs text-error" data-topup-error="payment_method"></p>
                            @error('payment_method') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div id="payment-method-details" class="rounded-2xl border border-outline-variant/15 bg-surface-container-lowest/40 p-4">
                            @foreach($paymentMethods as $pm)
                                @php
                                    $instructions = $pm->{"instructions_{$locale}"} ?: $pm->instructions_en;
                                @endphp
                                <div class="{{ $selectedMethod === $pm->method ? '' : 'hidden' }}" data-payment-detail="{{ $pm->method }}">
                                    <div class="font-label {{ $isArabic ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest' }} font-black text-outline">{{ $locale === 'ar' ? 'أرسل إلى' : 'Send To' }}</div>
                                    <div class="mt-1 break-all font-headline text-base font-black text-primary-container md:text-lg">{{ $pm->account_identifier }}</div>
                                    <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">
                                        {{ $instructions ?: ($locale === 'ar' ? 'ارفع صورة الإيصال بعد إتمام التحويل.' : 'Upload your receipt after completing the transfer.') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sf-field">
                                <label for="amount_requested">{{ $locale === 'ar' ? 'المبلغ بالدولار' : 'Amount in USD' }}</label>
                                <input type="number" name="amount_requested" id="amount_requested" min="1" max="10000" step="0.01" required value="{{ old('amount_requested') }}" placeholder="25.00">
                                <p class="mt-1 hidden text-xs text-error" data-topup-error="amount_requested"></p>
                                @error('amount_requested') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="sf-field">
                                <label for="receipt_image">{{ $locale === 'ar' ? 'صورة الإيصال' : 'Receipt Image' }}</label>
                                <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required class="text-sm text-on-surface-variant">
                                <p class="mt-1 hidden text-xs text-error" data-topup-error="receipt_image"></p>
                                @error('receipt_image') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" id="topup-submit-btn" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-4 font-headline text-xs font-black uppercase tracking-[0.18em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70 sm:text-sm sm:tracking-[0.2em]">
                            <span id="btn-text">{{ $locale === 'ar' ? 'إرسال طلب الشحن' : 'Submit Top-Up Request' }}</span>
                            <span id="btn-icon" class="material-symbols-outlined text-lg">bolt</span>
                        </button>
                    </form>
                @endif
            </div>

            @if($topupRequests->isNotEmpty())
                <div id="topup-panel" class="rounded-[1.5rem] border border-outline-variant/15 bg-surface-container-low/80 p-4 md:rounded-[2rem] md:p-5">
                    <h3 class="mb-3 font-headline text-sm font-black uppercase text-on-surface">{{ $locale === 'ar' ? 'طلبات الشحن' : 'Top-Up Requests' }}</h3>
                    <div id="topup-list" class="space-y-2">
                        @foreach($topupRequests as $topup)
                            @php
                                $statusStyles = [
                                    'pending' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-500',
                                    'approved' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-500',
                                    'rejected' => 'border-rose-500/20 bg-rose-500/10 text-rose-500',
                                ];
                                $statusLabels = [
                                    'pending' => $locale === 'ar' ? 'قيد الانتظار' : 'Pending',
                                    'approved' => $locale === 'ar' ? 'مقبول' : 'Approved',
                                    'rejected' => $locale === 'ar' ? 'مرفوض' : 'Rejected',
                                ];
                                $receiptUrl = $topup->getReceiptUrl();
                            @endphp
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-3" data-topup-status="{{ $topup->status }}" data-topup-status-label="{{ $statusLabels[$topup->status] ?? ucfirst($topup->status) }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($receiptUrl)
                                        <button type="button" onclick="openLightbox('{{ $receiptUrl }}')" class="group relative h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-outline-variant/20 transition hover:border-primary-container">
                                            <img src="{{ $receiptUrl }}" class="h-full w-full object-cover opacity-60 transition group-hover:opacity-100">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100">
                                                <span class="material-symbols-outlined text-sm text-white">visibility</span>
                                            </div>
                                        </button>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-headline text-sm font-black text-on-surface">${{ number_format($topup->amount_requested, 2) }}</div>
                                        <div class="font-label {{ $isArabic ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest' }} text-outline">{{ strtoupper($topup->payment_method) }}</div>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-full border px-3 py-1 font-label {{ $isArabic ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest' }} font-black {{ $statusStyles[$topup->status] ?? $statusStyles['pending'] }}">
                                    {{ $statusLabels[$topup->status] ?? ucfirst($topup->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($topupRequests->isEmpty())
                <div id="topup-panel" class="hidden rounded-[1.5rem] border border-outline-variant/15 bg-surface-container-low/80 p-4 md:rounded-[2rem] md:p-5">
                    <h3 class="mb-3 font-headline text-sm font-black uppercase text-on-surface">{{ $locale === 'ar' ? 'طلبات الشحن' : 'Top-Up Requests' }}</h3>
                    <div id="topup-list" class="space-y-2"></div>
                </div>
            @endif
        </div>

        <div class="rounded-[1.5rem] border border-outline-variant/15 bg-surface-container/80 p-4 shadow-2xl shadow-black/20 md:rounded-[2rem] md:p-6">
            <h2 class="mb-4 font-headline text-lg font-black uppercase text-on-surface md:mb-5 md:text-xl">{{ $locale === 'ar' ? 'سجل المحفظة' : 'Wallet History' }}</h2>

            @if(method_exists($transactions, 'isNotEmpty') ? $transactions->isNotEmpty() : $transactions->count() > 0)
                <div class="space-y-3 md:hidden">
                    @foreach($transactions as $tx)
                        @php
                            $type = $tx->type->value ?? $tx->type;
                            $isCredit = in_array($type, ['topup', 'refund'], true);
                        @endphp
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-4">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="sf-pill sf-pill-{{ $isCredit ? 'completed' : 'pending' }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                <span class="font-headline text-sm font-black" style="color: {{ $isCredit ? 'var(--sf-green)' : 'var(--sf-hot-red)' }};">
                                    {{ $isCredit ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between border-t border-outline-variant/10 pt-3">
                                <span class="font-label {{ $isArabic ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest' }} font-black text-outline">{{ $locale === 'ar' ? 'الرصيد بعد' : 'Balance After' }}</span>
                                <span class="font-headline text-sm font-black text-on-surface">${{ number_format($tx->balance_after, 2) }}</span>
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-on-surface-variant">{{ $tx->{"description_{$locale}"} ?? $tx->description_en }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto md:block">
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
                <div class="flex flex-col items-center justify-center rounded-3xl border border-outline-variant/10 bg-surface-container-lowest/30 p-10 text-center md:p-16">
                    <span class="material-symbols-outlined text-5xl text-outline/30 md:text-6xl">history</span>
                    <p class="mt-4 font-label text-[10px] font-black uppercase tracking-[0.24em] text-outline">{{ $locale === 'ar' ? 'لا توجد معاملات بعد' : 'No transactions yet' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

    </div>
</div>

{{-- Lightbox Overlay --}}
<div id="receipt-lightbox" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/90 p-4 backdrop-blur-md opacity-0 transition-opacity duration-300 pointer-events-none">
    <div class="relative max-h-[90vh] max-w-[90vw]">
        <button type="button" onclick="closeLightbox()" class="absolute -top-12 right-0 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20">
            <span class="material-symbols-outlined">close</span>
        </button>
        <img id="lightbox-img" src="" class="rounded-2xl shadow-2xl ring-1 ring-white/10 max-h-[85vh] object-contain">
    </div>
</div>

@push('scripts')
<script>
    function openLightbox(url) {
        const lightbox = document.getElementById('receipt-lightbox');
        const img = document.getElementById('lightbox-img');
        if (!lightbox || !img) return;

        img.src = url;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        
        // Trigger reflow for transition
        void lightbox.offsetWidth;
        
        lightbox.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('receipt-lightbox');
        if (!lightbox) return;

        lightbox.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            lightbox.classList.remove('flex');
            lightbox.classList.add('hidden');
        }, 300);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });

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

        const form = document.getElementById('topup-form');
        const submitBtn = document.getElementById('topup-submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');
        const responseBox = document.getElementById('topup-response');
        const topupPanel = document.getElementById('topup-panel');
        const topupList = document.getElementById('topup-list');
        const defaultBtnText = btnText?.textContent ?? '';
        const defaultBtnIcon = btnIcon?.textContent ?? 'bolt';
        const pendingLabel = @js($locale === 'ar' ? 'قيد الانتظار' : 'Pending');
        const badgeTextClass = @js($locale === 'ar' ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest');
        const methodTextClass = @js($locale === 'ar' ? 'text-xs tracking-normal' : 'text-[9px] uppercase tracking-widest');

        const statusClass = (status) => {
            if (status === 'approved') return 'border-emerald-500/20 bg-emerald-500/10 text-emerald-500';
            if (status === 'rejected') return 'border-rose-500/20 bg-rose-500/10 text-rose-500';

            return 'border-yellow-500/20 bg-yellow-500/10 text-yellow-500';
        };

        const setLoading = (isLoading) => {
            if (!submitBtn || !btnText || !btnIcon) return;

            submitBtn.disabled = isLoading;
            btnText.textContent = isLoading ? @js($locale === 'ar' ? 'جاري الإرسال...' : 'Processing...') : defaultBtnText;
            btnIcon.textContent = isLoading ? 'refresh' : defaultBtnIcon;
            btnIcon.classList.toggle('animate-spin', isLoading);
        };

        const clearErrors = () => {
            form?.querySelectorAll('[data-topup-error]').forEach((error) => {
                error.textContent = '';
                error.classList.add('hidden');
            });
        };

        const showResponse = (message, type = 'success') => {
            if (!responseBox) return;

            responseBox.textContent = message;
            responseBox.className = [
                'rounded-2xl border px-4 py-3 text-sm',
                type === 'success' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-500' : 'border-error/20 bg-error-container/10 text-error',
            ].join(' ');
        };

        const showErrors = (errors) => {
            Object.entries(errors || {}).forEach(([field, messages]) => {
                const error = form?.querySelector(`[data-topup-error="${field}"]`);
                if (!error) return;

                error.textContent = Array.isArray(messages) ? messages[0] : messages;
                error.classList.remove('hidden');
            });
        };

        const syncTopupBadges = () => {
            document.querySelectorAll('[data-topup-status]').forEach((row) => {
                const badge = row.querySelector('span:last-child');
                const status = row.dataset.topupStatus || 'pending';
                const label = row.dataset.topupStatusLabel || status;

                if (!badge) return;

                badge.className = `shrink-0 rounded-full border px-3 py-1 font-label ${badgeTextClass} font-black ${statusClass(status)}`;
                badge.textContent = label;
            });
        };

        const prependTopup = (topup) => {
            if (!topupList || !topup) return;

            topupPanel?.classList.remove('hidden');
            topupList.insertAdjacentHTML('afterbegin', `
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-3" data-topup-status="${topup.status || 'pending'}" data-topup-status-label="${topup.status_label || pendingLabel}">
                    <div class="min-w-0">
                        <div class="font-headline text-sm font-black text-on-surface">$${topup.amount_requested}</div>
                        <div class="font-label ${methodTextClass} text-outline">${topup.payment_method}</div>
                    </div>
                    <span class="shrink-0 rounded-full border px-3 py-1 font-label ${badgeTextClass} font-black ${statusClass(topup.status || 'pending')}">${topup.status_label || pendingLabel}</span>
                </div>
            `);
        };

        syncTopupBadges();

        if (form) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                event.stopImmediatePropagation();
                clearErrors();
                setLoading(true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        showErrors(payload.errors);
                        showResponse(payload.message || @js($locale === 'ar' ? 'تعذر إرسال طلب الشحن.' : 'Could not submit the top-up request.'), 'error');
                        return;
                    }

                    showResponse(payload.message || @js($locale === 'ar' ? 'تم إرسال طلب الشحن.' : 'Top-up request submitted.'));
                    prependTopup(payload.topup);
                    form.reset();
                    syncDetails();
                } catch (error) {
                    showResponse(@js($locale === 'ar' ? 'حدث خطأ في الاتصال. حاول مرة أخرى.' : 'Connection error. Please try again.'), 'error');
                } finally {
                    setLoading(false);
                }
            }, true);
        }

        if (form) {
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnText.textContent = @js($locale === 'ar' ? 'جاري الإرسال...' : 'Processing...');
                btnIcon.textContent = 'refresh';
                btnIcon.classList.add('animate-spin');
            });
        }
    });
</script>
@endpush
@endsection
