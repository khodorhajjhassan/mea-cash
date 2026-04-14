@extends('admin.layouts.app')
@section('title', 'Order Details')
@section('header', 'Order Details')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">Order #{{ $order->order_number ?: $order->id }}</h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    @if($order->status == 'completed') bg-green-100 text-green-700 
                    @elseif($order->status == 'failed') bg-red-100 text-red-700 
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div><p class="text-xs text-slate-500">Order Number</p><p class="font-medium text-slate-900">{{ $order->order_number ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">User</p><p class="font-medium text-slate-900">{{ $order->user?->name ?? 'Guest' }}</p></div>
            <div><p class="text-xs text-slate-500">Product</p><p class="font-medium text-slate-900">{{ $order->product?->name_en ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Transaction ID</p><p class="font-medium text-slate-900">{{ $order->transaction_id ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Created At</p><p class="font-medium text-slate-900">{{ $order->created_at->format('Y-m-d H:i') }}</p></div>
        </div>
    </section>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="panel">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Update Status</h3></div>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex gap-2 items-end">
                @csrf
                @method('PUT')
                <div class="field grow">
                    <label>New Status</label>
                    <select name="status" class="w-full">
                        @foreach(['pending', 'processing', 'completed', 'failed', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-primary">Save</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Actions</h3></div>
            <div class="mt-4 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.orders.fulfill', $order) }}">
                    @csrf
                    <button class="btn-ghost">Manual Fulfill</button>
                </form>
                <form method="POST" action="{{ route('admin.orders.refund', $order) }}">
                    @csrf
                    <button class="btn-danger-outline">Refund Order</button>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

