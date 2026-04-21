@extends('admin.layouts.app')
@section('title', 'Pending Orders')
@section('header', 'Pending Fulfillment')
@section('content')

<section class="panel">
    <form method="GET" action="{{ route('admin.orders.pending') }}" class="mb-4 grid gap-3 md:grid-cols-5">
        <div class="field md:col-span-1">
            <label>Search Orders</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order #, user...">
        </div>
        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="">All Pending</option>
                <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                <option value="processing" @selected(($filters['status'] ?? '') === 'processing')>Processing</option>
                <option value="reported" @selected(($filters['status'] ?? '') === 'reported')>Reported</option>
            </select>
        </div>
        <div class="field">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}">
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
            <button class="btn-primary grow" type="submit">Filter</button>
            <a class="btn-ghost" href="{{ route('admin.orders.pending') }}">Clear</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Product</th>
                    <th>User</th>
                    <th>Price</th>
                    <th>Waiting Time</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900">{{ $order->order_number ?: '#'.$order->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($order->product?->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($order->product->image) }}" class="h-8 w-8 rounded object-cover shadow-sm border border-slate-100">
                                @endif
                                <div>
                                    <p class="font-medium text-slate-800 leading-tight">{{ $order->product?->name_en ?? 'Unknown Product' }}</p>
                                    @if($order->package)
                                        <p class="text-[10px] text-slate-400 capitalize">{{ $order->package->name_en }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-600 font-medium">
                            <a href="{{ $order->user_id ? route('admin.users.show', $order->user_id) : '#' }}" class="text-indigo-600 hover:underline">{{ $order->user?->name ?? '-' }}</a>
                        </td>
                        <td class="font-semibold text-slate-900">${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-slate-50 border border-slate-200 text-xs font-medium text-slate-600">
                                <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $order->wait_time }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'reported' => 'bg-rose-100 text-rose-700',
                                    'failed' => 'bg-rose-100 text-rose-700',
                                    'refunded' => 'bg-rose-100 text-rose-700',
                                ];
                                $colorClass = $statusColors[$order->status->value] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                                {{ $order->status->value }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn-primary text-xs py-1 px-3">View & Fulfill</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 italic">No pending orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</section>
@endsection
