@extends('admin.layouts.app')
@section('title', 'User Dashboard')
@section('header', 'Customer Profile: ' . $user->name)
@section('content')

<div class="space-y-6">
    <!-- User Header & Stats -->
    <div class="grid gap-6 md:grid-cols-3">
        <section class="panel md:col-span-2 flex items-center gap-6">
            <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl shadow-inner">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-1 mt-2">
                    <div class="flex items-center gap-1.5 text-sm text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $user->phone ?: 'No phone' }}
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 0h7M11 21l5-10 5 10m-8.5-4h7M5 9a18.03 18.03 0 005.5 7.5M5 9a18.03 18.03 0 01-2 2.5"/></svg>
                        {{ $user->preferred_language === 'ar' ? 'Arabic' : 'English' }}
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2 text-right">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost text-xs uppercase font-bold">Edit Profile</a>
                @if((int) $user->id !== (int) auth()->id())
                    <button type="button" onclick="document.getElementById('deleteUserModal').classList.remove('hidden')" class="btn-danger-outline text-xs uppercase font-bold">
                        Delete User
                    </button>
                @endif
            </div>
        </section>

        <section class="panel bg-indigo-600 text-white overflow-hidden relative">
            <div class="relative z-10">
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Total Lifetime Spending</p>
                <h3 class="text-4xl font-black mt-2">${{ number_format($totalSpent, 2) }}</h3>
                <p class="text-indigo-200 text-[10px] mt-4">Calculated from completed orders</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 h-32 w-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.97 0-1.82 1.28-3.26 3.11-3.71V3.5h2.67v1.76c1.1.2 2.22.7 2.91 1.56.64.81.75 1.76.75 2.24h-1.89c-.01-.71-.31-1.43-.87-1.96-.64-.59-1.53-.78-2.58-.78-1.54 0-2.43.74-2.43 1.63 0 .74.45 1.41 2.28 1.84 2.19.52 4.57 1.25 4.57 4.12.01 2.06-1.35 3.51-3.15 3.97z"/></svg>
        </section>
    </div>

    <!-- Wallet and Activity Histoy -->
    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Wallet Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <section class="panel border-2 border-green-50">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-800">Wallet Balance</h3>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black text-green-600">${{ number_format($user->wallet?->balance ?? 0, 2) }}</div>
                    <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase tracking-tighter">Current available credit</p>
                </div>
                <button onclick="document.getElementById('refillModal').classList.remove('hidden')" class="btn-primary w-full mt-6 py-3 font-bold uppercase tracking-widest shadow-lg shadow-indigo-100">
                    Refill Wallet
                </button>
            </section>

            <section class="panel">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-800">Quick Actions</h3>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="mailto:{{ $user->email }}" class="btn-ghost w-full justify-start gap-3 text-sm">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Send Email
                    </a>
                    @if($user->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="btn-ghost w-full justify-start gap-3 text-sm">
                        <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        WhatsApp
                    </a>
                    @endif
                </div>
            </section>
        </div>

        <!-- History content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Recent Orders -->
            <section class="panel">
                <div class="panel-head border-b border-slate-100 pb-3 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index', ['q' => $user->name]) }}" class="text-xs font-bold text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-indigo-600 hover:underline">#{{ $order->order_number ?: $order->id }}</a></td>
                                <td class="text-sm font-medium">{{ $order->product?->name_en ?? 'N/A' }}</td>
                                <td class="font-bold text-slate-900">${{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'failed' => 'bg-rose-100 text-rose-700',
                                            'refunded' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $colorClass = $statusColors[$order->status->value] ?? 'bg-slate-100 text-slate-600';
                                    @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ $colorClass }}">
                                        {{ $order->status->value }}
                                    </span>
                                </td>
                                <td class="text-xs text-slate-500">{{ $order->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-400 italic font-medium border-none">No order history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="panel">
                <div class="panel-head border-b border-slate-100 pb-3 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800">Wallet Activity</h3>
                    <a href="{{ route('admin.transactions.index', ['q' => $user->name]) }}" class="text-xs font-bold text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Admin</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->wallet?->transactions ?? [] as $tx)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $tx) }}" class="text-slate-400 font-mono text-[10px] hover:text-indigo-600">#{{ $tx->id }}</a></td>
                                <td><span class="text-[10px] font-bold uppercase text-slate-600">{{ str_replace('_',' ',$tx->type->value) }}</span></td>
                                <td class="font-bold {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="text-[10px] font-medium text-slate-500 uppercase tracking-tighter">
                                    {{ $tx->processor?->name ?? 'System' }}
                                </td>
                                <td class="text-xs text-slate-500">{{ $tx->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-400 italic font-medium border-none">No wallet activity recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

@if((int) $user->id !== (int) auth()->id())
<div id="deleteUserModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="border-b border-rose-100 bg-rose-50 p-6 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                <span class="material-symbols-outlined">delete_forever</span>
            </div>
            <h3 class="text-xl font-black text-slate-900">Delete User</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                This will permanently delete {{ $user->name }} and linked wallet, orders, top-ups, and feedback.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-6">
            @csrf
            @method('DELETE')
            <div class="rounded-xl border border-rose-100 bg-rose-50/60 p-4 text-sm text-rose-700">
                This action cannot be undone.
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('deleteUserModal').classList.add('hidden')" class="btn-ghost flex-1 py-3">
                    Cancel
                </button>
                <button type="submit" class="btn-danger flex-[2] rounded-lg bg-rose-600 py-3 font-bold uppercase tracking-wider text-white hover:bg-rose-700">
                    Delete Permanently
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Refill Modal -->
<div id="refillModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden ring-1 ring-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h3 class="text-xl font-bold">Manual Wallet Refill</h3>
            <p class="text-indigo-100 text-xs mt-1">Directly credit {{ $user->name }}'s account.</p>
        </div>
        <form method="POST" action="{{ route('admin.users.credit', $user) }}" class="p-8 space-y-6">
            @csrf
            <div class="field">
                <label class="text-slate-900 font-bold">Amount to Add ($)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 font-bold">$</span>
                    <input type="number" name="amount" step="0.01" class="w-full pl-8 py-4 text-xl font-bold text-indigo-700 bg-indigo-50/50 border-indigo-100 rounded-xl" placeholder="0.00" autoFocus required>
                </div>
            </div>
            <div class="field">
                <label class="text-slate-900 font-bold text-sm">Reason / Reference</label>
                <input type="text" name="description" class="p-4" placeholder="e.g. Compensation, Balance Correction" required>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="button" onclick="document.getElementById('refillModal').classList.add('hidden')" class="btn-ghost flex-1 py-4 text-slate-400 font-bold uppercase tracking-wider text-xs">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white flex-[2] py-4 rounded-xl shadow-lg shadow-indigo-100 font-bold uppercase tracking-widest text-xs transition-all active:scale-[0.98]">Confirm Refill</button>
            </div>
        </form>
    </div>
</div>

@endsection
