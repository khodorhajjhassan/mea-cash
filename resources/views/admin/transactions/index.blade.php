@extends('admin.layouts.app')
@section('title','Transactions')
@section('header','Wallet Transactions')
@section('content')
@php
    $money = static fn ($value) => '$'.number_format((float) $value, 2);
    $summaryCards = [
        ['label' => 'Wallet In', 'value' => $summary['wallet_in'], 'tone' => 'text-emerald-600', 'hint' => 'Top-ups, refunds, positive adjustments'],
        ['label' => 'Wallet Out', 'value' => $summary['wallet_out'], 'tone' => 'text-rose-600', 'hint' => 'Purchases and negative adjustments'],
        ['label' => 'Net Flow', 'value' => $summary['net_wallet_flow'], 'tone' => $summary['net_wallet_flow'] >= 0 ? 'text-emerald-600' : 'text-rose-600', 'hint' => 'In minus out for selected period'],
        ['label' => 'Profit / Loss', 'value' => $summary['profit'], 'tone' => $summary['profit'] >= 0 ? 'text-indigo-600' : 'text-rose-600', 'hint' => 'Completed order revenue minus cost'],
    ];
@endphp

<section class="space-y-6">
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($summaryCards as $card)
            <div class="panel">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-black {{ $card['tone'] }}">{{ $money($card['value']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Top-ups</p>
            <p class="mt-2 text-lg font-black text-emerald-700">{{ $money($summary['topups']) }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Purchases</p>
            <p class="mt-2 text-lg font-black text-blue-700">{{ $money($summary['purchases']) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Cost</p>
            <p class="mt-2 text-lg font-black text-amber-700">{{ $money($summary['cost']) }}</p>
        </div>
        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Refunded</p>
            <p class="mt-2 text-lg font-black text-rose-700">{{ $money($summary['refunded_order_value']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Pending Value</p>
            <p class="mt-2 text-lg font-black text-slate-700">{{ $money($summary['pending_order_value']) }}</p>
        </div>
    </div>

    <div class="panel space-y-6">
    <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 md:grid-cols-6">
        <div class="field md:col-span-1">
            <label>User / Description</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search...">
        </div>
        <div class="field">
            <label>Type</label>
            <select name="type">
                <option value="">All Types</option>
                @foreach(['topup','purchase','refund','admin_adjustment'] as $type)
                    <option value="{{ $type }}" @selected(($filters['type'] ?? '')===$type)>{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Direction</label>
            <select name="direction">
                <option value="">In & Out</option>
                <option value="in" @selected(($filters['direction'] ?? '') === 'in')>Money In</option>
                <option value="out" @selected(($filters['direction'] ?? '') === 'out')>Money Out</option>
            </select>
        </div>
        <div class="field">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}">
        </div>
        <div class="field">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary flex-1" type="submit">Filter</button>
            <a class="btn-ghost" href="{{ route('admin.transactions.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Processed By</th>
                    <th>When</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td><span class="text-xs font-mono text-slate-400">#{{ $tx->id }}</span></td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $tx->wallet?->user?->name ?? 'Unknown' }}</div>
                        <div class="text-[10px] text-slate-400">ID: {{ $tx->wallet?->user?->id ?? '-' }}</div>
                    </td>
                    <td>
                        @php
                            $typeColors = [
                                'topup' => 'bg-emerald-100 text-emerald-700',
                                'purchase' => 'bg-blue-100 text-blue-700',
                                'refund' => 'bg-rose-100 text-rose-700',
                                'admin_adjustment' => 'bg-amber-100 text-amber-700',
                            ];
                            $colorClass = $typeColors[$tx->type->value] ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ $colorClass }}">
                            {{ str_replace('_',' ',$tx->type->value) }}
                        </span>
                    </td>
                    <td>
                        <span class="font-bold @if($tx->amount > 0) text-green-600 @else text-red-600 @endif">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                        </span>
                    </td>
                    <td>
                        @if($tx->processor)
                            <span class="text-xs text-slate-600 font-medium">Admin: {{ $tx->processor->name }}</span>
                        @else
                            <span class="text-[10px] text-slate-400 italic">System Auto</span>
                        @endif
                    </td>
                    <td class="text-xs text-slate-500">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.transactions.show', $tx) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wider">Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-slate-400 italic">No transactions found matching your criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</section>
@endsection
