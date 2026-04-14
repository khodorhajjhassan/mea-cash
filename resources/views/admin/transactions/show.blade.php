@extends('admin.layouts.app')
@section('title', 'Transaction Details')
@section('header', 'Transaction #' . $transaction->id)
@section('content')

<div class="grid gap-6 max-w-4xl">
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3">
            <h2 class="text-lg font-semibold text-slate-900">Information</h2>
            <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full 
                @if($transaction->type == 'topup') bg-green-100 text-green-700 
                @elseif($transaction->type == 'purchase') bg-blue-100 text-blue-700 
                @elseif($transaction->type == 'refund') bg-indigo-100 text-indigo-700
                @else bg-slate-100 text-slate-600 @endif">
                {{ str_replace('_', ' ', $transaction->type) }}
            </span>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">User</label>
                    <p class="text-slate-900 font-semibold">{{ $transaction->wallet?->user?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-slate-500">{{ $transaction->wallet?->user?->email }}</p>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Amount</label>
                    <p class="text-2xl font-bold @if($transaction->amount > 0) text-green-600 @else text-red-600 @endif">
                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                    </p>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Balance Change</label>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-slate-500 font-medium">${{ number_format($transaction->balance_before, 2) }}</span>
                        <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <span class="text-indigo-600 font-bold">${{ number_format($transaction->balance_after, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Description</label>
                    <p class="text-slate-800 italic text-sm leading-relaxed mt-1">"{{ $transaction->description_en ?: 'No description provided.' }}"</p>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Timestamp</label>
                    <p class="text-slate-900 font-medium text-sm">{{ $transaction->created_at->format('M d, Y - H:i:s') }}</p>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Processed By</label>
                    <p class="text-slate-900 font-medium text-sm flex items-center gap-2">
                        @if($transaction->processor)
                            <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Admin: {{ $transaction->processor->name }}
                        @else
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            System Automated
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if($transaction->reference)
        <div class="mt-8 pt-6 border-t border-slate-100">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Linked Resource</label>
            <div class="mt-2 flex items-center justify-between p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        @if($transaction->reference_type == 'App\Models\Order')
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @elseif($transaction->reference_type == 'App\Models\TopupRequest')
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ str_replace('App\\Models\\', '', $transaction->reference_type) }} #{{ $transaction->reference_id }}</p>
                        <p class="text-[10px] text-slate-500 font-medium">Click to view linked details</p>
                    </div>
                </div>
                <a href="{{ 
                    $transaction->reference_type == 'App\Models\Order' 
                        ? route('admin.orders.show', $transaction->reference_id) 
                        : ($transaction->reference_type == 'App\Models\TopupRequest' ? route('admin.topups.show', $transaction->reference_id) : '#') 
                    }}" 
                    class="btn-ghost text-xs">View Reference</a>
            </div>
        </div>
        @endif
    </section>

    <div class="flex justify-between items-center">
        <a href="{{ route('admin.transactions.index') }}" class="btn-ghost">Back to List</a>
        @if($transaction->wallet?->user)
            <a href="{{ route('admin.users.show', $transaction->wallet->user) }}" class="btn-primary">View User Profile</a>
        @endif
    </div>
</div>

@endsection
