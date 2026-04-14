@extends('admin.layouts.app')
@section('title', 'Pending Orders')
@section('header', 'Pending Fulfillment')
@section('content')

<section class="panel">
    <form method="GET" action="{{ route('admin.orders.pending') }}" class="mb-4 grid gap-3 md:grid-cols-4">
        <div class="field md:col-span-3">
            <label>Search Orders</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order #, User name, or Product...">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary grow" type="submit">Search</button>
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
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $order->status }}
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

