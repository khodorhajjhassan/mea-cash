@extends('admin.layouts.app')
@section('title','User Transactions')
@section('header','User Transaction History')
@section('content')
<section class="panel"><p class="mb-4 text-sm text-slate-600">User: {{ $user->name }} (#{{ $user->id }})</p>
<form method="GET" action="{{ route('admin.transactions.user', $user) }}" class="mb-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Description"></div>
<div class="field"><label>Type</label><select name="type"><option value="">All</option>@foreach(['topup','purchase','refund','admin_adjustment'] as $type)<option value="{{ $type }}" @selected(($filters['type'] ?? '')===$type)>{{ ucfirst(str_replace('_',' ',$type)) }}</option>@endforeach</select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.transactions.user', $user) }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Type</th><th>Amount</th><th>Balance After</th><th>Description</th><th>When</th></tr></thead><tbody>@forelse($transactions as $tx)<tr><td>#{{ $tx->id }}</td><td><span class="text-[10px] font-bold uppercase text-slate-600">{{ $tx->typeLabel() }}</span></td><td class="font-bold {{ $tx->isCredit() ? 'text-green-600' : 'text-red-600' }}">{{ $tx->signedAmountLabel() }}</td><td>${{ number_format((float) $tx->balance_after, 2) }}</td><td class="text-xs text-slate-500">{{ $tx->description_en ?: $tx->description_ar ?: '-' }}</td><td>{{ $tx->created_at->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="6">No transactions found.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $transactions->links() }}</div></section>
@endsection
