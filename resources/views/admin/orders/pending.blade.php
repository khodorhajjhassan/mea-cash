@extends('admin.layouts.app')
@section('title','Pending Orders')
@section('header','Pending Fulfillment')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>No.</th><th>User</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td>{{ $order->user?->name ?? '-' }}</td><td>{{ $order->status }}</td><td class="flex gap-2"><form method="POST" action="{{ route('admin.orders.fulfill',$order) }}">@csrf<button class="btn-primary">Fulfill</button></form><form method="POST" action="{{ route('admin.orders.fail',$order) }}">@csrf<button class="btn-danger-outline">Fail</button></form></td></tr>
@empty <tr><td colspan="4">No pending orders.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $orders->links() }}</div></section>
@endsection
