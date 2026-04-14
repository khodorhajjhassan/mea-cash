@extends('admin.layouts.app')
@section('title','Orders')
@section('header','All Orders')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold">Orders</h2><a href="{{ route('admin.orders.pending') }}" class="btn-ghost">Pending</a></div>

<form method="GET" action="{{ route('admin.orders.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order number, user, product"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option>@foreach(['pending','processing','completed','failed','refunded'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.orders.index') }}">Reset</a></div>
</form>

<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>No.</th><th>User</th><th>Product</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td><a href="{{ $order->user_id ? route('admin.users.show', $order->user_id) : '#' }}" class="text-indigo-600 hover:underline">{{ $order->user?->name ?? '-' }}</a></td><td>{{ $order->product?->name_en ?? '-' }}</td><td>${{ number_format((float)$order->total_price,2) }}</td><td>{{ $order->status }}</td><td class="flex gap-2"><a class="btn-ghost" href="{{ route('admin.orders.show',$order) }}">View</a></td></tr>
@empty <tr><td colspan="6">No orders found.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $orders->links() }}</div></section>
@endsection
