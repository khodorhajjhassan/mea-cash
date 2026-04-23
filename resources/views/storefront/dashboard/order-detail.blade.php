@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل الطلب - MeaCash' : 'Order Detail - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $status = $order->status->value ?? $order->status;
    $reportStatus = $order->report?->status;
    $hasReport = (bool) $order->report;
    $isRefunded = $status === 'refunded' || $reportStatus === 'refunded';
    $displayStatus = $isRefunded
        ? 'refunded'
        : ($reportStatus === 'resolved'
            ? 'resolved'
            : ($hasReport ? 'reported' : $status));
    $statusSteps = [
        'pending' => [
            'label_en' => 'Pending',
            'label_ar' => 'قيد الانتظار',
            'icon' => 'pending_actions',
            'tone' => 'text-yellow-400 border-yellow-400/40 bg-yellow-400/10',
        ],
        'processing' => [
            'label_en' => 'Processing',
            'label_ar' => 'قيد المعالجة',
            'icon' => 'hourglass_top',
            'tone' => 'text-sky-400 border-sky-400/40 bg-sky-400/10',
        ],
        'completed' => [
            'label_en' => 'Completed',
            'label_ar' => 'مكتمل',
            'icon' => 'verified',
            'tone' => 'text-emerald-400 border-emerald-400/45 bg-emerald-400/10',
        ],
        'refunded' => [
            'label_en' => 'Refunded',
            'label_ar' => 'مسترد',
            'icon' => 'currency_exchange',
            'tone' => 'text-orange-300 border-orange-300/40 bg-orange-300/10',
        ],
        'failed' => [
            'label_en' => 'Failed',
            'label_ar' => 'فشل',
            'icon' => 'error',
            'tone' => 'text-red-400 border-red-400/40 bg-red-400/10',
        ],
    ];
    unset($statusSteps['refunded']);
    unset($statusSteps['failed']);

    if ($hasReport && !$isRefunded && $reportStatus !== 'resolved') {
        $statusSteps['reported'] = [
            'label_en' => 'Reported',
            'label_ar' => 'تم الإبلاغ',
            'icon' => 'support_agent',
            'tone' => 'text-rose-300 border-rose-300/40 bg-rose-300/10',
        ];
    }

    if ($reportStatus === 'resolved') {
        $statusSteps['resolved'] = [
            'label_en' => 'Resolved',
            'label_ar' => 'تم الحل',
            'icon' => 'task_alt',
            'tone' => 'text-emerald-300 border-emerald-300/40 bg-emerald-300/10',
        ];
    }

    if ($isRefunded) {
        $statusSteps['refunded'] = [
            'label_en' => 'Refunded',
            'label_ar' => 'مسترد',
            'icon' => 'currency_exchange',
            'tone' => 'text-orange-300 border-orange-300/40 bg-orange-300/10',
        ];
    }

    if ($status === 'failed') {
        $statusSteps['failed'] = [
            'label_en' => 'Failed',
            'label_ar' => 'فشل',
            'icon' => 'error',
            'tone' => 'text-red-400 border-red-400/40 bg-red-400/10',
        ];
    }

    $statusOrder = array_keys($statusSteps);
    $currentStatusIndex = array_search($displayStatus, $statusOrder, true);
    $currentStatusIndex = $currentStatusIndex === false ? 0 : $currentStatusIndex;
    $currentStatusLabel = $isArabic
        ? ($statusSteps[$displayStatus]['label_ar'] ?? $displayStatus)
        : ucfirst($displayStatus);
    $reportStatusLabel = $isArabic
        ? match ($order->report?->status) {
            'open' => 'مفتوح',
            'reviewing' => 'قيد المراجعة',
            'resolved' => 'تم الحل',
            'refunded' => 'مسترد',
            default => str_replace('_', ' ', (string) ($order->report?->status ?? '')),
        }
        : str_replace('_', ' ', (string) ($order->report?->status ?? ''));
    $fulfillmentDetails = $order->getFulfillmentDetails();
    $fulfillmentData = array_filter((array) ($fulfillmentDetails['data'] ?? []), fn ($value) => filled($value));
    $userInput = array_filter($order->getUserInput(), fn ($value) => filled($value));
    $canShowCompletedOrderActions = in_array($status, ['completed', 'reported'], true) && !$isRefunded;
    $supportReportWindowHours = $supportReportDelayHours ?? 4;
    $supportReportExpiresAt = $supportReportWindowHours > 0
        ? ($order->fulfilled_at ?? $order->created_at)->copy()->addHours($supportReportWindowHours)
        : null;
    $supportReportWindowOpen = $supportReportExpiresAt === null || now()->lte($supportReportExpiresAt);
    $blockingReportOpen = $hasReport && in_array($reportStatus, ['open', 'reviewing'], true);
    $canOpenSupportReport = $canShowCompletedOrderActions
        && !$blockingReportOpen
        && !$isRefunded
        && $supportReportWindowOpen;
@endphp

<div class="relative mx-auto max-w-[1440px] px-4 py-10 md:px-8 animate-fade-in">
    {{-- Decorative Background --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_15%_20%,rgba(0,240,255,0.08),transparent_30%),radial-gradient(circle_at_85%_8%,rgba(254,0,254,0.06),transparent_28%)] blur-3xl"></div>

    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('store.orders') }}" class="group flex items-center gap-2 font-label text-[10px] font-black uppercase tracking-widest text-outline transition hover:text-primary-container">
            <span class="material-symbols-outlined text-sm transition-transform group-hover:-translate-x-1">arrow_back</span>
            <span>{{ $locale === 'ar' ? 'العودة للطلبات' : 'Back to Orders' }}</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'معرف النظام' : 'Internal ID' }}:</span>
            <span class="font-mono text-[10px] text-on-surface/40">#{{ $order->id }}</span>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[32px] border-outline-variant/10 shadow-2xl">
        {{-- Header Section --}}
        <div class="border-b border-outline-variant/10 bg-surface-container/20 px-6 py-8 md:px-10">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="font-headline text-3xl font-black italic uppercase tracking-tighter text-on-surface md:text-4xl">
                        {{ $order->order_number }}
                    </h1>
                    <div class="mt-2 flex items-center gap-3 text-outline">
                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                        <span class="font-label text-[10px] font-bold uppercase tracking-wider">{{ $order->created_at->format('M d, Y \at h:i A') }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="inline-flex items-center rounded-full px-4 py-1.5 {{ $isArabic ? 'text-sm tracking-normal' : 'text-[10px] uppercase tracking-[0.15em]' }} font-black
                            {{ in_array($displayStatus, ['completed', 'resolved'], true) ? 'bg-emerald-400 text-background shadow-[0_0_18px_rgba(52,211,153,0.35)]' :
                               (in_array($displayStatus, ['pending', 'processing'], true) ? 'bg-yellow-500 text-background' : ($displayStatus === 'reported' ? 'bg-rose-400 text-background' : 'bg-red-500 text-white')) }}">
                            {{ $currentStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-b border-outline-variant/10 bg-surface-container-lowest/20 px-6 py-6 md:px-10">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="font-headline text-xs font-black uppercase tracking-widest text-primary-container">
                        {{ $locale === 'ar' ? 'مسار الطلب' : 'Order Process' }}
                    </p>
                    <p class="mt-1 text-[11px] text-outline">
                        {{ $locale === 'ar' ? 'تابع حالة طلبك خطوة بخطوة.' : 'Track your order status step by step.' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-1 sm:gap-4 items-start">
                @foreach($statusSteps as $stepKey => $step)
                    @php
                        $stepIndex = array_search($stepKey, $statusOrder, true);
                        $isCurrent = $displayStatus === $stepKey;
                        $isPassed = $stepIndex <= $currentStatusIndex && !$isCurrent;
                        $isTerminal = $isCurrent && in_array($stepKey, ['failed', 'refunded', 'reported', 'resolved'], true);
                    @endphp
                    <div class="relative transition-all {{ $isCurrent || $isPassed || $isTerminal ? ($stepKey === 'completed' ? 'text-emerald-400' : 'text-primary-container') : 'text-outline/40' }}">
                        @if(!$loop->first)
                            <span class="absolute -start-1/2 top-6 h-px w-full bg-outline-variant/10 -z-10 hidden sm:block"></span>
                        @endif
                        <div class="flex flex-col items-center text-center">
                            <span class="material-symbols-outlined !flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full border text-[18px] sm:text-[22px] leading-none mb-2 {{ $isCurrent || $isPassed || $isTerminal ? $step['tone'] . ($stepKey === 'completed' ? ' shadow-[0_0_20px_rgba(52,211,153,0.15)]' : ' shadow-[0_0_20px_rgba(0,240,255,0.12)]') : 'border-outline-variant/10 bg-surface-container-low text-outline/30' }}">
                                {{ $step['icon'] }}
                            </span>
                            <div class="px-1">
                                <p class="font-headline {{ $isArabic ? 'text-[10px] sm:text-xs tracking-normal' : 'text-[8px] sm:text-[10px] uppercase tracking-tight sm:tracking-widest' }} font-black leading-tight">
                                    {{ $locale === 'ar' ? $step['label_ar'] : $step['label_en'] }}
                                </p>
                                <p class="mt-0.5 font-label text-[7px] font-black uppercase tracking-tighter opacity-50 hidden sm:block">
                                    {{ $isCurrent ? ($locale === 'ar' ? 'الحالة الحالية' : 'Current') : ($isPassed ? ($locale === 'ar' ? 'تمت' : 'Done') : ($locale === 'ar' ? 'لاحقا' : 'Next')) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-6 py-8 md:px-10">
            {{-- Order Summary --}}
            <div class="grid gap-10 md:grid-cols-2">
                <div class="space-y-6">
                    <h3 class="font-headline text-xs font-black uppercase tracking-widest text-primary-container">{{ $locale === 'ar' ? 'ملخص المنتج' : 'Product Asset' }}</h3>
                    <div class="flex items-center gap-4 rounded-2xl bg-surface-container-low p-4 border border-outline-variant/10">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-surface-container-highest border border-outline-variant/20">
                            @if($order->product?->image)
                                @php($productImage = str_starts_with($order->product->image, 'http') ? $order->product->image : \Illuminate\Support\Facades\Storage::url($order->product->image))
                                <img src="{{ $productImage }}" alt="{{ $order->product?->{"name_{$locale}"} ?? 'Product' }}" class="h-full w-full rounded-xl object-cover" loading="lazy">
                            @else
                                <span class="material-symbols-outlined text-2xl text-primary-container/60">receipt_long</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="font-headline text-lg font-black uppercase text-on-surface truncate">{{ $order->product?->{"name_{$locale}"} ?? 'Unknown Product' }}</div>
                            @if($order->package)
                                <div class="mt-1 text-[11px] font-black uppercase tracking-widest text-outline">{{ $order->package->{"name_{$locale}"} }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="divide-y divide-outline-variant/10">
                        @foreach($userInput as $key => $value)
                            <div class="flex justify-between gap-4 py-3">
                                <span class="font-label text-[10px] font-bold uppercase tracking-wider text-outline">{{ $order->product?->resolvedFieldLabel($key, $locale) ?? str_replace('_', ' ', $key) }}</span>
                                <span class="max-w-[55%] text-end font-headline text-sm font-black text-on-surface break-words">
                                    @if(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-3">
                            <span class="font-label text-[10px] font-bold uppercase tracking-wider text-outline">{{ $locale === 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</span>
                            <span class="font-headline text-xs font-black text-on-surface">${{ number_format($order->unit_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-4 border-t-2 border-primary-container/20">
                            <span class="font-label text-xs font-black uppercase tracking-[0.2em] text-primary-container">{{ $locale === 'ar' ? 'المجموع الإجمالي' : 'Total Amount' }}</span>
                            <span class="font-headline text-3xl font-black italic text-primary-container">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="font-headline text-xs font-black uppercase tracking-widest text-primary-container">{{ $locale === 'ar' ? 'تفاصيل التسليم' : 'Delivery Status' }}</h3>
                    
                    @if($canShowCompletedOrderActions && ($order->items->isNotEmpty() || !empty($fulfillmentData) || filled($fulfillmentDetails['admin_note'] ?? null)))
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="group relative rounded-2xl border border-primary-container/20 bg-primary-container/5 p-4 transition-all hover:bg-primary-container/10">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="font-label text-[9px] font-black uppercase tracking-[0.2em] text-primary-container/70">{{ ucfirst($item->type ?? 'Access Key') }}</span>
                                        <div class="h-1.5 w-1.5 rounded-full bg-primary-container animate-pulse"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0 flex-grow">
                                            <div class="code-reveal blur-md select-none font-mono text-sm font-bold tracking-wider text-on-surface break-all transition-all duration-300" data-val="{{ $item->delivered_value }}">
                                                ••••••••••••••••
                                            </div>
                                        </div>
                                        <button onclick="revealCode(this)" class="shrink-0 rounded-lg bg-surface-container-highest px-4 py-2 font-label text-[10px] font-black uppercase tracking-widest text-primary-container hover:bg-primary-container hover:text-on-primary-container transition-all">
                                            {{ $locale === 'ar' ? 'كشف' : 'Reveal' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($fulfillmentData as $key => $value)
                                <div class="rounded-2xl border border-primary-container/20 bg-primary-container/5 p-4">
                                    <div class="mb-2 font-label text-[9px] font-black uppercase tracking-[0.2em] text-primary-container/70">
                                        {{ str_replace('_', ' ', $key) }}
                                    </div>
                                    <div class="font-mono text-sm font-bold tracking-wider text-on-surface break-all">
                                        @if(is_array($value))
                                            {{ implode(', ', $value) }}
                                        @else
                                            <div class="prose prose-invert max-w-none text-sm leading-relaxed text-on-surface">
                                                {!! $value !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if(filled($fulfillmentDetails['admin_note'] ?? null))
                                <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/50 p-4">
                                    <div class="mb-2 font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'ملاحظة الإدارة' : 'Admin Note' }}</div>
                                    <div class="prose prose-invert max-w-none text-[11px] leading-relaxed text-on-surface-variant">{!! $fulfillmentDetails['admin_note'] !!}</div>
                                </div>
                            @endif
                        </div>
                        <script>
                            function revealCode(btn) {
                                const codeDiv = btn.parentElement.querySelector('.code-reveal');
                                codeDiv.classList.remove('blur-md', 'select-none');
                                codeDiv.textContent = codeDiv.dataset.val;
                                btn.remove();
                                // Optional: copy to clipboard
                                navigator.clipboard.writeText(codeDiv.dataset.val);
                            }
                        </script>
                    @elseif($status === 'pending' || $status === 'processing')
                        <div class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-yellow-500/20 bg-yellow-500/5 p-8 text-center">
                            <div class="relative">
                                <span class="material-symbols-outlined text-4xl text-yellow-500">hourglass_empty</span>
                                <div class="absolute inset-0 animate-ping rounded-full border border-yellow-500/30"></div>
                            </div>
                            <p class="font-headline text-[10px] font-black uppercase tracking-[0.1em] text-on-surface-variant">
                                {{ $locale === 'ar' ? 'طلبك قيد المعالجة الآن' : 'Encryption in progress...' }}
                            </p>
                        </div>
                @elseif($canShowCompletedOrderActions)
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low p-6 text-center">
                            <span class="material-symbols-outlined mb-2 text-3xl text-outline/30">info</span>
                            <p class="text-xs text-outline">{{ $locale === 'ar' ? 'لا توجد بيانات تسليم متاحة حالياً.' : 'No delivery data available for this status.' }}</p>
                        </div>
                    @endif

                    @if($order->delivery_notes)
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/50 p-4">
                            <div class="mb-2 font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'ملاحظات' : 'Special Instructions' }}</div>
                            <p class="text-[11px] leading-relaxed text-on-surface-variant">{!! nl2br(e($order->delivery_notes)) !!}</p>
                        </div>
                    @endif

                    @if($isRefunded && filled($order->refund_notes))
                        <div class="rounded-2xl border border-orange-300/20 bg-orange-300/10 p-4">
                            <div class="mb-2 flex items-center gap-2 font-label text-[9px] font-black uppercase tracking-widest text-orange-300">
                                <span class="material-symbols-outlined text-sm">currency_exchange</span>
                                <span>{{ $locale === 'ar' ? 'سبب الاسترداد' : 'Refund Reason' }}</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-on-surface-variant">{!! nl2br(e($order->refund_notes)) !!}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($canShowCompletedOrderActions)
            <div class="border-t border-outline-variant/10 bg-surface-container-lowest/20 px-6 py-6 md:px-10">
                <div class="flex flex-col gap-4 rounded-3xl border border-primary-container/15 bg-primary-container/5 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-container/10 text-primary-container">
                            <span class="material-symbols-outlined">reviews</span>
                        </div>
                        <div>
                            <p class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">
                                {{ $locale === 'ar' ? 'تقييم الطلب' : 'Order Feedback' }}
                            </p>
                            <p class="mt-1 text-[11px] leading-relaxed text-outline">
                                @if($order->feedback)
                                    {{ $locale === 'ar' ? 'شكراً لك، تم حفظ تقييمك لهذا الطلب.' : 'Thank you, your feedback is saved for this completed order.' }}
                                @else
                                    {{ $locale === 'ar' ? 'شاركنا تجربتك بعد اكتمال الطلب.' : 'Share your experience now that this order is completed.' }}
                                @endif
                                    @if(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-3">
                            <span class="font-label text-[10px] font-bold uppercase tracking-wider text-outline">{{ $locale === 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</span>
                            <span class="font-headline text-xs font-black text-on-surface">${{ number_format($order->unit_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-4 border-t-2 border-primary-container/20">
                            <span class="font-label text-xs font-black uppercase tracking-[0.2em] text-primary-container">{{ $locale === 'ar' ? 'المجموع الإجمالي' : 'Total Amount' }}</span>
                            <span class="font-headline text-3xl font-black italic text-primary-container">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="font-headline text-xs font-black uppercase tracking-widest text-primary-container">{{ $locale === 'ar' ? 'تفاصيل التسليم' : 'Delivery Status' }}</h3>
                    
                    @if($canShowCompletedOrderActions && ($order->items->isNotEmpty() || !empty($fulfillmentData) || filled($fulfillmentDetails['admin_note'] ?? null)))
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="group relative rounded-2xl border border-primary-container/20 bg-primary-container/5 p-4 transition-all hover:bg-primary-container/10">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="font-label text-[9px] font-black uppercase tracking-[0.2em] text-primary-container/70">{{ ucfirst($item->type ?? 'Access Key') }}</span>
                                        <div class="h-1.5 w-1.5 rounded-full bg-primary-container animate-pulse"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0 flex-grow">
                                            <div class="code-reveal blur-md select-none font-mono text-sm font-bold tracking-wider text-on-surface break-all transition-all duration-300" data-val="{{ $item->delivered_value }}">
                                                ••••••••••••••••
                                            </div>
                                        </div>
                                        <button onclick="revealCode(this)" class="shrink-0 rounded-lg bg-surface-container-highest px-4 py-2 font-label text-[10px] font-black uppercase tracking-widest text-primary-container hover:bg-primary-container hover:text-on-primary-container transition-all">
                                            {{ $locale === 'ar' ? 'كشف' : 'Reveal' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($fulfillmentData as $key => $value)
                                <div class="rounded-2xl border border-primary-container/20 bg-primary-container/5 p-4">
                                    <div class="mb-2 font-label text-[9px] font-black uppercase tracking-[0.2em] text-primary-container/70">
                                        {{ str_replace('_', ' ', $key) }}
                                    </div>
                                    <div class="font-mono text-sm font-bold tracking-wider text-on-surface break-all">
                                        @if(is_array($value))
                                            {{ implode(', ', $value) }}
                                        @else
                                            <div class="prose prose-invert max-w-none text-sm leading-relaxed text-on-surface">
                                                {!! $value !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if(filled($fulfillmentDetails['admin_note'] ?? null))
                                <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/50 p-4">
                                    <div class="mb-2 font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'ملاحظة الإدارة' : 'Admin Note' }}</div>
                                    <div class="prose prose-invert max-w-none text-[11px] leading-relaxed text-on-surface-variant">{!! $fulfillmentDetails['admin_note'] !!}</div>
                                </div>
                            @endif
                        </div>
                        <script>
                            function revealCode(btn) {
                                const codeDiv = btn.parentElement.querySelector('.code-reveal');
                                codeDiv.classList.remove('blur-md', 'select-none');
                                codeDiv.textContent = codeDiv.dataset.val;
                                btn.remove();
                                // Optional: copy to clipboard
                                navigator.clipboard.writeText(codeDiv.dataset.val);
                            }
                        </script>
                    @elseif($status === 'pending' || $status === 'processing')
                        <div class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-yellow-500/20 bg-yellow-500/5 p-8 text-center">
                            <div class="relative">
                                <span class="material-symbols-outlined text-4xl text-yellow-500">hourglass_empty</span>
                                <div class="absolute inset-0 animate-ping rounded-full border border-yellow-500/30"></div>
                            </div>
                            <p class="font-headline text-[10px] font-black uppercase tracking-[0.1em] text-on-surface-variant">
                                {{ $locale === 'ar' ? 'طلبك قيد المعالجة الآن' : 'Encryption in progress...' }}
                            </p>
                        </div>
                @elseif($canShowCompletedOrderActions)
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low p-6 text-center">
                            <span class="material-symbols-outlined mb-2 text-3xl text-outline/30">info</span>
                            <p class="text-xs text-outline">{{ $locale === 'ar' ? 'لا توجد بيانات تسليم متاحة حالياً.' : 'No delivery data available for this status.' }}</p>
                        </div>
                    @endif

                    @if($order->delivery_notes)
                        <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-low/50 p-4">
                            <div class="mb-2 font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'ملاحظات' : 'Special Instructions' }}</div>
                            <p class="text-[11px] leading-relaxed text-on-surface-variant">{!! nl2br(e($order->delivery_notes)) !!}</p>
                        </div>
                    @endif

                    @if($isRefunded && filled($order->refund_notes))
                        <div class="rounded-2xl border border-orange-300/20 bg-orange-300/10 p-4">
                            <div class="mb-2 flex items-center gap-2 font-label text-[9px] font-black uppercase tracking-widest text-orange-300">
                                <span class="material-symbols-outlined text-sm">currency_exchange</span>
                                <span>{{ $locale === 'ar' ? 'سبب الاسترداد' : 'Refund Reason' }}</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-on-surface-variant">{!! nl2br(e($order->refund_notes)) !!}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($canShowCompletedOrderActions)
            <div class="border-t border-outline-variant/10 bg-surface-container-lowest/20 px-6 py-6 md:px-10">
                <div class="flex flex-col gap-4 rounded-3xl border border-primary-container/15 bg-primary-container/5 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-container/10 text-primary-container">
                            <span class="material-symbols-outlined">reviews</span>
                        </div>
                        <div>
                            <p class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">
                                {{ $locale === 'ar' ? 'تقييم الطلب' : 'Order Feedback' }}
                            </p>
                            <p class="mt-1 text-[11px] leading-relaxed text-outline">
                                @if($order->feedback)
                                    {{ $locale === 'ar' ? 'شكراً لك، تم حفظ تقييمك لهذا الطلب.' : 'Thank you, your feedback is saved for this completed order.' }}
                                @else
                                    {{ $locale === 'ar' ? 'شاركنا تجربتك بعد اكتمال الطلب.' : 'Share your experience now that this order is completed.' }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($order->feedback)
                        <div class="flex items-center gap-1 rounded-full border border-yellow-400/20 bg-yellow-400/10 px-4 py-2 text-yellow-300">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-base">{{ $i <= $order->feedback->rating ? 'star' : 'star_outline' }}</span>
                            @endfor
                        </div>
                @elseif($canShowCompletedOrderActions)
                        <button type="button" onclick="document.getElementById('feedbackModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-3 font-headline text-[11px] font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                            <span>{{ $locale === 'ar' ? 'أضف تقييمك' : 'Leave Feedback' }}</span>
                            <span class="material-symbols-outlined text-base">rate_review</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Footer/Support Area --}}
        <div class="border-t border-outline-variant/10 bg-surface-container/10 px-6 py-6 md:px-10">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/10">
                        <span class="material-symbols-outlined text-sm text-secondary-container">support_agent</span>
                    </div>
                    <span class="font-label text-[10px] font-bold uppercase tracking-wider text-outline">{{ $locale === 'ar' ? 'هل تواجه مشكلة؟' : 'Need assistance?' }}</span>
                </div>
                @if($order->report)
                        <div class="flex max-w-full flex-col items-end gap-2 text-end">
                        <span class="rounded-full border border-secondary-container/20 bg-secondary-container/10 px-4 py-2 font-headline {{ $isArabic ? 'text-sm tracking-normal' : 'text-[10px] uppercase tracking-widest' }} font-black text-secondary-container">
                            {{ $locale === 'ar' ? 'بلاغ الدعم' : 'Support Report' }}: {{ $reportStatusLabel }}
                        </span>
                        @if(filled($order->report->admin_response))
                            <div class="prose prose-invert max-w-md text-xs leading-relaxed text-on-surface-variant">{!! $order->report->admin_response !!}</div>
                        @endif
                        @if($canOpenSupportReport)
                            <button type="button" onclick="document.getElementById('reportModal').classList.remove('hidden')" class="font-headline text-[10px] font-black uppercase tracking-widest text-primary-container hover:underline">
                                {{ $locale === 'ar' ? 'فتح بلاغ جديد' : 'Open New Report' }}
                            </button>
                        @endif
                        </div>
                @elseif($canOpenSupportReport)
                        <button type="button" onclick="document.getElementById('reportModal').classList.remove('hidden')" class="font-headline text-[10px] font-black uppercase tracking-widest text-primary-container hover:underline">
                            {{ $locale === 'ar' ? 'فتح بلاغ دعم' : 'Open Support Report' }}
                        </button>
                @elseif($canShowCompletedOrderActions && !$supportReportWindowOpen)
                    <span class="max-w-md text-end text-xs leading-relaxed text-on-surface-variant">
                        {{ $locale === 'ar' ? "انتهت مدة فتح بلاغ الدعم لهذا الطلب." : "The support report window for this order has expired." }}
                    </span>
                @else
                    <a href="{{ route('store.contact') }}" class="font-headline text-[10px] font-black uppercase tracking-widest text-primary-container hover:underline">
                        {{ $locale === 'ar' ? 'تواصل مع الدعم الفني' : 'Contact Support' }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@if($canShowCompletedOrderActions && !$order->feedback)
    <div id="feedbackModal" class="feedback-modal fixed inset-0 z-[100] hidden items-center justify-center bg-background/85 p-4 backdrop-blur-xl">
        <div class="absolute inset-0" onclick="document.getElementById('feedbackModal').classList.add('hidden')"></div>
        <form method="POST" action="{{ route('store.orders.feedback', $order) }}" class="relative w-full max-w-md rounded-[28px] border border-outline-variant/20 bg-surface-container p-6 shadow-[0_28px_90px_rgba(0,0,0,0.65)]">
            @csrf
            <input type="hidden" name="is_ar" value="{{ $locale === 'ar' ? 1 : 0 }}">

            <button type="button" onclick="document.getElementById('feedbackModal').classList.add('hidden')" class="absolute end-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-lowest text-outline transition hover:text-secondary-container">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>

            <div class="mb-6 pe-10">
                <p class="font-headline text-xl font-black uppercase tracking-tight text-on-surface">
                    {{ $locale === 'ar' ? 'قيّم تجربتك' : 'Rate Your Experience' }}
                </p>
                <p class="mt-2 text-xs leading-relaxed text-on-surface-variant">
                    {{ $locale === 'ar' ? 'هذا التقييم مرتبط بطلب حقيقي مكتمل فقط.' : 'This feedback is tied to this real completed order only.' }}
                </p>
            </div>

            <div class="mb-5">
                <label class="mb-3 block font-label text-[10px] font-black uppercase tracking-widest text-primary-container">
                    {{ $locale === 'ar' ? 'التقييم' : 'Rating' }}
                </label>
                <div class="flex flex-row-reverse justify-end gap-1 feedback-stars">
                    @for($i = 5; $i >= 1; $i--)
                        <input id="feedback-star-{{ $i }}" type="radio" name="rating" value="{{ $i }}" class="sr-only" required @checked($i === 5)>
                        <label for="feedback-star-{{ $i }}" class="cursor-pointer text-3xl text-outline/30 transition hover:scale-110 hover:text-yellow-300">
                            <span class="material-symbols-outlined">star</span>
                        </label>
                    @endfor
                </div>
                <p class="mt-2 text-[10px] text-outline">{{ $locale === 'ar' ? 'اختر من 1 إلى 5 نجوم.' : 'Choose from 1 to 5 stars.' }}</p>
            </div>

            <div class="mb-6">
                <label class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-primary-container">
                    {{ $locale === 'ar' ? 'تعليقك' : 'Comment' }}
                </label>
                <textarea name="comment" rows="4" maxlength="1000" class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container" placeholder="{{ $locale === 'ar' ? 'اكتب ملاحظتك هنا...' : 'Tell us what went well or what we can improve...' }}"></textarea>
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-4 font-headline text-xs font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                {{ $locale === 'ar' ? 'إرسال التقييم' : 'Submit Feedback' }}
                <span class="material-symbols-outlined text-base">send</span>
            </button>
        </form>
    </div>
@endif

@if($canOpenSupportReport)
    <div id="reportModal" class="feedback-modal fixed inset-0 z-[100] hidden items-center justify-center bg-background/85 p-4 backdrop-blur-xl">
        <div class="absolute inset-0" onclick="document.getElementById('reportModal').classList.add('hidden')"></div>
        <form method="POST" action="{{ route('store.orders.report', $order) }}" class="relative w-full max-w-md rounded-[28px] border border-outline-variant/20 bg-surface-container p-6 shadow-[0_28px_90px_rgba(0,0,0,0.65)]">
            @csrf
            <input type="hidden" name="is_ar" value="{{ $locale === 'ar' ? 1 : 0 }}">

            <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')" class="absolute end-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-lowest text-outline transition hover:text-secondary-container">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>

            <div class="mb-6 pe-10">
                <p class="font-headline text-xl font-black uppercase tracking-tight text-on-surface">
                    {{ $locale === 'ar' ? 'بلاغ مشكلة في الطلب' : 'Report Order Problem' }}
                </p>
                <p class="mt-2 text-xs leading-relaxed text-on-surface-variant">
                    {{ $locale === 'ar' ? 'استخدم هذا إذا كان المفتاح لا يعمل أو توجد مشكلة في الحساب.' : 'Use this if the key does not work or there is a problem with the account.' }}
                </p>
            </div>

            <div class="mb-5">
                <label class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-primary-container">
                    {{ $locale === 'ar' ? 'نوع المشكلة' : 'Issue Type' }}
                </label>
                <select name="issue_type" required class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
                    <option value="key_not_working">{{ $locale === 'ar' ? 'المفتاح لا يعمل' : 'Key does not work' }}</option>
                    <option value="account_problem">{{ $locale === 'ar' ? 'مشكلة في الحساب' : 'Account problem' }}</option>
                    <option value="wrong_details">{{ $locale === 'ar' ? 'بيانات غير صحيحة' : 'Wrong details' }}</option>
                    <option value="other">{{ $locale === 'ar' ? 'أخرى' : 'Other' }}</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="mb-2 block font-label text-[10px] font-black uppercase tracking-widest text-primary-container">
                    {{ $locale === 'ar' ? 'اشرح المشكلة' : 'Problem Details' }}
                </label>
                <textarea name="comment" rows="5" maxlength="2000" required class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container" placeholder="{{ $locale === 'ar' ? 'اكتب ماذا حدث معك...' : 'Tell support what happened...' }}"></textarea>
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-5 py-4 font-headline text-xs font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                {{ $locale === 'ar' ? 'إرسال البلاغ' : 'Submit Report' }}
                <span class="material-symbols-outlined text-base">support_agent</span>
            </button>
        </form>
    </div>
@endif
@endsection
