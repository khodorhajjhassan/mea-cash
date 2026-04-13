@extends('admin.layouts.app')
@section('title','Pending Orders')
@section('header','Pending Fulfillment')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.orders.pending') }}" class="mb-4 grid gap-3 md:grid-cols-3">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order number, user, product"></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.orders.pending') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>No.</th><th>User</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td>{{ $order->user?->name ?? '-' }}</td><td>{{ $order->status }}</td><td class="flex gap-2"><form method="POST" action="{{ route('admin.orders.fulfill',$order) }}">@csrf<button class="btn-primary">Fulfill</button></form><form method="POST" action="{{ route('admin.orders.fail',$order) }}">@csrf<button class="btn-danger-outline">Fail</button></form></td></tr>
@empty <tr><td colspan="4">No pending orders.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $orders->links() }}</div></section>
@endsection
