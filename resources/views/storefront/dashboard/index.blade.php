@extends('storefront.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة التحكم - MeaCash' : 'Dashboard - MeaCash')

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<div class="relative mx-auto max-w-[1440px] px-4 py-8 md:px-8 animate-fade-in">
    <div class="pointer-events-none absolute start-0 top-0 h-96 w-96 light-leak-cyan opacity-10 blur-[120px]"></div>

    <div class="mb-12">
        <span class="mb-2 block font-label text-[10px] uppercase tracking-[0.3em] text-primary-container">
            {{ $locale === 'ar' ? 'مساحة المستخدم' : 'Account Dashboard' }}
        </span>
        <h1 class="mb-2 font-headline text-4xl font-black italic uppercase leading-none tracking-tighter md:text-5xl">
            {{ $locale === 'ar' ? 'مرحباً، ' . auth()->user()->name : 'Welcome, ' . auth()->user()->name }}
        </h1>
        <p class="text-sm text-on-surface-variant opacity-70">
            {{ $locale === 'ar' ? 'رقم المستخدم' : 'User ID' }}:
            <span class="font-bold text-on-surface">#{{ auth()->id() }}</span>
        </p>
    </div>

    <div class="mb-12 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="glass-panel rounded-[24px] border-primary-container/10 p-6 transition-all duration-300 hover:border-primary-container/30">
            <div class="mb-4 flex items-start justify-between">
                <span class="material-symbols-outlined text-3xl text-primary-container/40">account_balance_wallet</span>
                <span class="rounded-full bg-primary-container/10 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-primary-container">Active</span>
            </div>
            <div class="mb-1 font-label text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ $locale === 'ar' ? 'رصيد المحفظة' : 'Wallet Balance' }}</div>
            <div class="font-headline text-3xl font-black italic text-primary-container">${{ number_format($balance, 2) }}</div>
        </div>

        <div class="glass-panel rounded-[24px] border-secondary-container/10 p-6 transition-all duration-300 hover:border-secondary-container/30">
            <div class="mb-4 flex items-start justify-between">
                <span class="material-symbols-outlined text-3xl text-secondary-container/40">terminal</span>
            </div>
            <div class="mb-1 font-label text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ $locale === 'ar' ? 'إجمالي الطلبات' : 'Total Orders' }}</div>
            <div class="font-headline text-3xl font-black italic text-on-surface">{{ $totalOrders }}</div>
        </div>

        <div class="glass-panel rounded-[24px] border-outline-variant/10 p-6 transition-all duration-300 hover:border-outline-variant/30">
            <div class="mb-4 flex items-start justify-between">
                <span class="material-symbols-outlined text-3xl text-outline/40">payments</span>
            </div>
            <div class="mb-1 font-label text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ $locale === 'ar' ? 'إجمالي الإنفاق' : 'Total Spent' }}</div>
            <div class="font-headline text-3xl font-black italic text-on-surface">${{ number_format($totalSpent, 2) }}</div>
        </div>

        <a href="{{ route('store.wallet') }}" class="glass-panel flex flex-col items-center justify-center rounded-[24px] border-2 border-dashed border-primary-container/10 bg-primary-container/5 p-6 text-center transition-all hover:border-primary-container/40 hover:bg-primary-container/10">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary-container shadow-[0_0_20px_rgba(0,240,255,0.4)]">
                <span class="material-symbols-outlined text-2xl font-black text-background">add</span>
            </div>
            <div class="font-label text-[10px] font-black uppercase tracking-[0.2em] text-primary-container">{{ $locale === 'ar' ? 'شحن المحفظة' : 'Top Up Wallet' }}</div>
        </a>
    </div>

    <div class="mb-10 grid grid-cols-2 gap-6 md:grid-cols-3">
        @php
            $navLinks = [
                ['name' => 'My Orders', 'ar' => 'طلباتي', 'route' => 'store.orders', 'icon' => 'box', 'bg' => 'bg-primary-container/5'],
                ['name' => 'Wallet', 'ar' => 'محفظتي', 'route' => 'store.wallet', 'icon' => 'account_balance_wallet', 'bg' => 'bg-secondary-container/5'],
                ['name' => 'Profile', 'ar' => 'ملفي الشخصي', 'route' => 'store.profile', 'icon' => 'person', 'bg' => 'bg-surface-container-highest'],
            ];
        @endphp
        @foreach($navLinks as $link)
            <a href="{{ route($link['route']) }}" class="group flex flex-col items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-full {{ $link['bg'] }} border border-outline-variant/10 shadow-xl transition-all duration-500 group-hover:scale-110 group-hover:border-primary-container/50">
                    <span class="material-symbols-outlined text-3xl text-on-surface/50 transition-colors group-hover:text-primary-container">{{ $link['icon'] }}</span>
                </div>
                <span class="font-headline text-xs font-black uppercase tracking-widest text-on-surface-variant group-hover:text-primary-container">{{ $locale === 'ar' ? $link['ar'] : $link['name'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="glass-panel mb-12 rounded-[32px] border-outline-variant/10 p-6 shadow-2xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary-container">monitoring</span>
                    <h2 class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">
                        {{ $locale === 'ar' ? 'إحصائيات المتجر' : 'Marketplace Statistics' }}
                    </h2>
                </div>
                <p class="mt-2 max-w-2xl text-xs leading-relaxed text-on-surface-variant">
                    {{ $locale === 'ar' ? 'فلتر نشاطك حسب التاريخ وشاهد ملخص طلباتك وإنفاقك.' : 'Filter your activity by date and review your orders, spending, and purchased products.' }}
                </p>
            </div>

            <form method="GET" action="{{ route('store.dashboard') }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                <div class="sf-field">
                    <label for="from">{{ $locale === 'ar' ? 'من' : 'From' }}</label>
                    <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="sf-field">
                    <label for="to">{{ $locale === 'ar' ? 'إلى' : 'To' }}</label>
                    <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
                </div>
                <button type="submit" class="self-end rounded-2xl bg-primary-container px-5 py-3 font-label text-[10px] font-black uppercase tracking-widest text-on-primary-container transition hover:scale-[1.02]">
                    {{ $locale === 'ar' ? 'فلترة' : 'Filter' }}
                </button>
                <a href="{{ route('store.dashboard') }}" class="self-end rounded-2xl border border-outline-variant/20 px-5 py-3 text-center font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant transition hover:border-primary-container/50 hover:text-primary-container">
                    {{ $locale === 'ar' ? 'إعادة' : 'Reset' }}
                </a>
            </form>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-5">
            <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/30 p-4">
                <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'الطلبات' : 'Orders' }}</div>
                <div class="mt-2 font-headline text-2xl font-black text-on-surface">{{ $marketplaceStats['orders'] }}</div>
            </div>
            <div class="rounded-2xl border border-primary-container/15 bg-primary-container/5 p-4">
                <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'الإنفاق' : 'Spent' }}</div>
                <div class="mt-2 font-headline text-2xl font-black text-primary-container">${{ number_format($marketplaceStats['spent'], 2) }}</div>
            </div>
            <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/30 p-4">
                <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'مكتملة' : 'Completed' }}</div>
                <div class="mt-2 font-headline text-2xl font-black text-on-surface">{{ $marketplaceStats['completed'] }}</div>
            </div>
            <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/30 p-4">
                <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'قيد المعالجة' : 'Pending' }}</div>
                <div class="mt-2 font-headline text-2xl font-black text-on-surface">{{ $marketplaceStats['pending'] }}</div>
            </div>
            <div class="rounded-2xl border border-secondary-container/15 bg-secondary-container/5 p-4">
                <div class="font-label text-[9px] font-black uppercase tracking-widest text-outline">{{ $locale === 'ar' ? 'منتجات' : 'Products' }}</div>
                <div class="mt-2 font-headline text-2xl font-black text-secondary-container">{{ $marketplaceStats['products'] }}</div>
            </div>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[32px] border-outline-variant/10 shadow-2xl">
        <div class="flex items-center justify-between border-b border-outline-variant/10 bg-surface-container/20 px-8 py-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary-container">list_alt</span>
                <h2 class="font-headline text-sm font-black uppercase tracking-widest text-on-surface">
                    {{ $locale === 'ar' ? 'آخر الطلبات' : 'Latest Orders' }}
                </h2>
            </div>
            <a href="{{ route('store.orders') }}" class="font-label text-[10px] font-black uppercase tracking-widest text-primary-container transition-colors hover:text-secondary-container">
                {{ $locale === 'ar' ? 'عرض الكل' : 'View All' }}
            </a>
        </div>

        <div class="overflow-x-auto no-scrollbar">
            @if($recentOrders->isNotEmpty())
                <table class="w-full text-start">
                    <thead>
                        <tr class="bg-surface-container-highest/30 text-[10px] font-black uppercase tracking-widest text-outline">
                            <th class="py-4 ps-8 text-start">{{ $locale === 'ar' ? 'الطلب' : 'Order' }}</th>
                            <th class="py-4 text-start">{{ $locale === 'ar' ? 'التاريخ' : 'Date' }}</th>
                            <th class="py-4 text-start">{{ $locale === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                            <th class="py-4 pe-8 text-end">{{ $locale === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        @foreach($recentOrders as $order)
                            <tr class="group cursor-pointer transition-colors hover:bg-white/5" onclick="window.location.href='{{ route('store.orders.detail', $order->order_number) }}'">
                                <td class="py-5 ps-8">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-outline-variant/10 bg-surface-container">
                                            <span class="material-symbols-outlined text-lg text-primary-container/60">receipt_long</span>
                                        </div>
                                        <div>
                                            <div class="font-headline text-xs font-black uppercase text-on-surface">{{ $order->order_number }}</div>
                                            <div class="max-w-[180px] truncate text-[10px] font-bold uppercase text-on-surface-variant">{{ $order->product?->name_en }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">{{ $order->created_at->format('d M, Y H:i') }}</span>
                                </td>
                                <td class="py-5 font-headline text-xs font-black text-primary-container">${{ number_format($order->total_price, 2) }}</td>
                                <td class="py-5 pe-8 text-end">
                                    @php $status = $order->status->value ?? $order->status; @endphp
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest
                                        {{ $status === 'completed' ? 'border border-primary-container/20 bg-primary-container/10 text-primary-container' :
                                           ($status === 'pending' ? 'border border-yellow-500/20 bg-yellow-500/10 text-yellow-500' : 'border border-red-500/20 bg-red-500/10 text-red-500') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="flex flex-col items-center justify-center gap-4 p-20 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline/20">history</span>
                    <p class="font-label text-[10px] font-bold uppercase tracking-[0.2em] text-outline">{{ $locale === 'ar' ? 'لا توجد طلبات حالياً' : 'No orders found' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
