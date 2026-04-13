@extends('admin.layouts.app')
@section('title','Orders')
@section('header','All Orders')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold">Orders</h2><a href="{{ route('admin.orders.pending') }}" class="btn-ghost">Pending</a></div>
<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>No.</th><th>User</th><th>Product</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td>{{ $order->user?->name ?? '-' }}</td><td>{{ $order->product?->name_en ?? '-' }}</td><td>${{ number_format((float)$order->total_price,2) }}</td><td>{{ $order->status }}</td><td class="flex gap-2"><a class="btn-ghost" href="{{ route('admin.orders.show',$order) }}">View</a></td></tr>
@empty <tr><td colspan="6">No orders found.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $orders->links() }}</div></section>
@endsection
