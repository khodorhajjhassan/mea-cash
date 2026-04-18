@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'طلباتي - MeaCash' : 'My Orders - MeaCash')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="relative mx-auto max-w-[1440px] px-4 py-10 md:px-8 animate-fade-in">
    {{-- Decorative Background --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(circle_at_15%_20%,rgba(0,240,255,0.1),transparent_30%),radial-gradient(circle_at_85%_8%,rgba(254,0,254,0.08),transparent_28%)] blur-3xl"></div>

    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="mb-2 block font-label text-[10px] uppercase tracking-[0.3em] text-primary-container">
                {{ $locale === 'ar' ? 'تاريخ المشتريات' : 'Purchase History' }}
            </span>
            <h1 class="font-headline text-4xl font-black italic uppercase leading-none tracking-tighter md:text-5xl">
                {{ $locale === 'ar' ? 'طلباتي' : 'My Orders' }}
            </h1>
        </div>
        <a href="{{ route('store.dashboard') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-6 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant transition hover:border-primary-container/50 hover:text-primary-container">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>{{ $locale === 'ar' ? 'العودة للوحة التحكم' : 'Back to Dashboard' }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('store.orders') }}" class="mb-6 rounded-[28px] border border-outline-variant/10 bg-surface-container-low/70 p-4 shadow-[0_18px_60px_rgba(0,0,0,0.18)] backdrop-blur">
        <div class="grid gap-3 md:grid-cols-4">
            <div>
                <label for="from" class="mb-2 block font-label text-[9px] font-black uppercase tracking-[0.18em] text-outline">
                    {{ $locale === 'ar' ? 'من تاريخ' : 'From Date' }}
                </label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
            </div>

            <div>
                <label for="to" class="mb-2 block font-label text-[9px] font-black uppercase tracking-[0.18em] text-outline">
                    {{ $locale === 'ar' ? 'إلى تاريخ' : 'To Date' }}
                </label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
            </div>

            <div>
                <label for="status" class="mb-2 block font-label text-[9px] font-black uppercase tracking-[0.18em] text-outline">
                    {{ $locale === 'ar' ? 'الحالة' : 'Status' }}
                </label>
                <select id="status" name="status" class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container">
                    <option value="">{{ $locale === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                    @foreach(['pending', 'processing', 'completed', 'refunded', 'failed'] as $statusOption)
                        <option value="{{ $statusOption }}" @selected(($filters['status'] ?? '') === $statusOption)>
                            {{ ucfirst($statusOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex min-h-12 flex-1 items-center justify-center rounded-2xl bg-primary-container px-5 py-3 font-headline text-[10px] font-black uppercase tracking-[0.18em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                    {{ $locale === 'ar' ? 'تطبيق' : 'Apply' }}
                </button>
                <a href="{{ route('store.orders') }}" class="flex min-h-12 items-center justify-center rounded-2xl border border-outline-variant/15 bg-surface-container-lowest px-4 py-3 font-label text-[10px] font-black uppercase tracking-widest text-outline transition hover:border-secondary-container/40 hover:text-secondary-container">
                    {{ $locale === 'ar' ? 'إعادة' : 'Reset' }}
                </a>
            </div>
        </div>
    </form>

    @if($orders->isNotEmpty())
        <div class="glass-panel overflow-hidden rounded-[32px] border-outline-variant/10 shadow-2xl">
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-start">
                    <thead>
                        <tr class="bg-surface-container-highest/30 text-[10px] font-black uppercase tracking-widest text-outline">
                            <th class="py-5 ps-8 text-start">{{ $locale === 'ar' ? 'رقم الطلب' : 'Order' }}</th>
                            <th class="py-5 text-start">{{ $locale === 'ar' ? 'المنتج' : 'Product' }}</th>
                            <th class="py-5 text-start">{{ $locale === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                            <th class="py-5 text-start">{{ $locale === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="py-5 pe-8 text-end">{{ $locale === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        @foreach($orders as $order)
                            <tr class="group cursor-pointer transition-colors hover:bg-white/5" onclick="window.location.href='{{ route('store.orders.detail', $order->order_number) }}'">
                                <td class="py-6 ps-8">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-outline-variant/10 bg-surface-container transition-transform group-hover:scale-110">
                                            <span class="material-symbols-outlined text-lg text-primary-container">receipt_long</span>
                                        </div>
                                        <div>
                                            <div class="font-headline text-sm font-black uppercase text-on-surface group-hover:text-primary-container">#{{ $order->order_number }}</div>
                                            <div class="text-[9px] font-bold uppercase tracking-widest text-outline">Ref: {{ substr($order->id, 0, 8) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6">
                                    <div class="font-headline text-xs font-bold uppercase text-on-surface-variant group-hover:text-on-surface">
                                        {{ $order->product?->{"name_{$locale}"} ?? '-' }}
                                    </div>
                                    @if($order->package)
                                        <div class="mt-1 text-[9px] font-black uppercase tracking-wider text-primary-container/70">{{ $order->package->{"name_{$locale}"} }}</div>
                                    @endif
                                </td>
                                <td class="py-6 font-headline text-sm font-black text-on-surface">
                                    ${{ number_format($order->total_price, 2) }}
                                </td>
                                <td class="py-6">
                                    @php $status = $order->status->value ?? $order->status; @endphp
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest
                                        {{ $status === 'completed' ? 'border border-primary-container/20 bg-primary-container/10 text-primary-container' :
                                           ($status === 'pending' || $status === 'processing' ? 'border border-yellow-500/20 bg-yellow-500/10 text-yellow-500' : 'border border-red-500/20 bg-red-500/10 text-red-500') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="py-6 pe-8 text-end">
                                    <span class="font-label text-[10px] font-bold uppercase tracking-wider text-outline">{{ $order->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 flex justify-center">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @else
        <div class="glass-panel flex flex-col items-center justify-center gap-6 rounded-[40px] border-outline-variant/10 p-24 text-center shadow-2xl">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-surface-container-highest/50 border border-outline-variant/10">
                <span class="material-symbols-outlined text-5xl text-outline/30">inventory_2</span>
            </div>
            <div>
                <h3 class="font-headline text-xl font-black uppercase text-on-surface">{{ $locale === 'ar' ? 'لا توجد طلبات بعد' : 'Zero Assets Found' }}</h3>
                <p class="mt-2 text-sm text-outline">{{ $locale === 'ar' ? 'ابدأ التسوق لتظهر مشترياتك هنا.' : 'Your digital vault is empty. Start shopping to fill it.' }}</p>
            </div>
            <a href="{{ route('store.home') }}" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-primary-container px-8 py-4 font-headline text-xs font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-105 active:scale-95">
                {{ $locale === 'ar' ? 'تسوق الآن' : 'Initiate Secure Purchase' }}
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </a>
        </div>
    @endif
</div>
@endsection
