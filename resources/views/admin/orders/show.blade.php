@extends('admin.layouts.app')
@section('title','Order Details')
@section('header','Order Details')
@section('content')
<section class="panel space-y-4">
<p><strong>Order:</strong> {{ $order->order_number ?: '#'.$order->id }}</p>
<p><strong>User:</strong> {{ $order->user?->name }}</p>
<p><strong>Product:</strong> {{ $order->product?->name_en }}</p>
<p><strong>Status:</strong> {{ $order->status }}</p>
<form method="POST" action="{{ route('admin.orders.status',$order) }}" class="flex gap-2 items-end">@csrf @method('PUT')
  <div class="field"><label>Update Status</label><select name="status">@foreach(['pending','processing','completed','failed','refunded'] as $status)<option value="{{ $status }}" @selected($order->status===$status)>{{ $status }}</option>@endforeach</select></div>
  <button class="btn-primary">Save</button>
</form>
<div class="flex gap-2"><form method="POST" action="{{ route('admin.orders.refund',$order) }}">@csrf<button class="btn-danger-outline">Refund</button></form><form method="POST" action="{{ route('admin.orders.fulfill',$order) }}">@csrf<button class="btn-ghost">Fulfill</button></form></div>
</section>
@endsection
