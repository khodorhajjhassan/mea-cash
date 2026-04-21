@extends('admin.layouts.app')
@section('title','Orders')
@section('header','All Orders')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold">Orders</h2><a href="{{ route('admin.orders.pending') }}" class="btn-ghost">Pending</a></div>

<form method="GET" action="{{ route('admin.orders.index') }}" class="mt-4 grid gap-3 md:grid-cols-5">
    <div class="field md:col-span-1"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order #, user..."></div>
    <div class="field"><label>Status</label><select name="status"><option value="">All</option>@foreach(['pending','processing','completed','reported','failed','refunded'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
    <div class="field"><label>From</label><input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"></div>
    <div class="field"><label>To</label><input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"></div>
    <div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.orders.index') }}">Reset</a></div>
</form>

<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>No.</th><th>User</th><th>Product</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)
    @php
        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'reported' => 'bg-rose-100 text-rose-700',
            'failed' => 'bg-rose-100 text-rose-700',
            'refunded' => 'bg-rose-100 text-rose-700',
            'canceled' => 'bg-slate-100 text-slate-500',
        ];
        $colorClass = $statusColors[$order->status->value] ?? 'bg-slate-100 text-slate-600';
    @endphp
    <tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td><a href="{{ $order->user_id ? route('admin.users.show', $order->user_id) : '#' }}" class="text-indigo-600 hover:underline">{{ $order->user?->name ?? '-' }}</a></td><td>{{ $order->product?->name_en ?? '-' }}</td><td>${{ number_format((float)$order->total_price,2) }}</td><td><span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">{{ $order->status->value }}</span></td><td class="flex gap-2"><a class="btn-ghost" href="{{ route('admin.orders.show',$order) }}">View</a></td></tr>
@empty <tr><td colspan="6">No orders found.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $orders->links() }}</div></section>
@endsection
