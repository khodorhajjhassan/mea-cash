@extends('admin.layouts.app')
@section('title', 'Order Details')
@section('header', 'Order Details')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head">
            <div class="flex items-center gap-4">
                @if($order->product?->image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($order->product->image) }}" class="h-12 w-12 rounded-lg object-cover shadow-sm border border-slate-100">
                @endif
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Order #{{ $order->order_number ?: $order->id }}</h2>
                    <p class="text-xs text-slate-500">{{ $order->product?->name_en ?? 'Unknown Product' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    @if($order->status == 'completed') bg-green-100 text-green-700 
                    @elseif($order->status == 'failed') bg-red-100 text-red-700 
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div><p class="text-xs text-slate-500">Wait Time</p><p class="font-medium text-slate-900">{{ $order->wait_time }}</p></div>
            <div><p class="text-xs text-slate-500">User</p><p class="font-medium text-slate-900"><a href="{{ $order->user_id ? route('admin.users.show', $order->user_id) : '#' }}" class="text-indigo-600 hover:underline">{{ $order->user?->name ?? 'Guest' }}</a></p></div>
            <div><p class="text-xs text-slate-500">Email</p><p class="font-medium text-slate-600">{{ $order->user?->email ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Phone</p><p class="font-medium text-slate-600">{{ $order->user?->phone ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Price</p><p class="font-semibold text-indigo-600">${{ number_format($order->total_price, 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Quantity</p><p class="font-medium text-slate-900">{{ $order->quantity }}</p></div>
            <div><p class="text-xs text-slate-500">Transaction ID</p><p class="font-medium text-slate-900">{{ $order->transaction_id ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Created At</p><p class="font-medium text-slate-900">{{ $order->created_at->format('Y-m-d H:i') }}</p></div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- User Submission Details -->
        <div class="lg:col-span-1 space-y-6">
            <section class="panel h-full">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900">User Submission</h3>
                </div>
                <div class="mt-4 space-y-4">
                    @php($userInput = $order->getuserinput())
                    @php($fields = $order->product?->formFields ?? collect())
                    @forelse($userInput as $key => $value)
                        @php($field = $fields->firstWhere('field_key', $key))
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">{{ $field?->label_en ?? str_replace('_', ' ', $key) }}</p>
                            <p class="text-sm font-medium text-slate-900 break-all">{{ is_array($value) ? json_encode($value) : $value }}</p>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 italic text-sm">No specific user data provided at checkout.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Fulfillment Flow -->
        <div class="lg:col-span-2 space-y-6">
            @if($order->status !== 'completed')
            <section class="panel border-2 border-indigo-50 leading-relaxed shadow-sm">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-indigo-900">Order Fulfillment</h3>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded uppercase font-bold tracking-tight">
                        {{ str_replace('_', ' ', $order->product?->product_type ?? 'Manual') }}
                    </span>
                </div>
                
                <form method="POST" action="{{ route('admin.orders.fulfill', $order) }}" class="mt-6 space-y-5">
                    @csrf
                    
                    <!-- Dynamic Fields based on Type -->
                    <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                        @if($order->isTypeKey())
                            <input type="hidden" name="fulfillment_type" value="key">
                            <div class="field">
                                <label class="text-indigo-900 font-semibold">Digital Keys / Codes</label>
                                <textarea name="keys" rows="5" placeholder="Enter one or multiple keys here..." class="w-full" required></textarea>
                                <p class="text-xs text-slate-500 mt-1">These will be delivered to the user as pure text/links.</p>
                            </div>
                        @elseif($order->isTypeAccount())
                            <input type="hidden" name="fulfillment_type" value="account">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="field"><label class="text-indigo-900 font-semibold">Account Email/User</label><input type="text" name="account_user" required></div>
                                <div class="field"><label class="text-indigo-900 font-semibold">Account Password</label><input type="text" name="account_pass" required></div>
                                <div class="field md:col-span-2"><label class="text-indigo-900 font-semibold">Login Link (Optional)</label><input type="url" name="account_link" placeholder="https://..."></div>
                            </div>
                        @else
                            <input type="hidden" name="fulfillment_type" value="topup">
                            <div class="field">
                                <label class="text-indigo-900 font-semibold">Transaction Reference / ID</label>
                                <input type="text" name="transaction_id" required placeholder="e.g. TXN12345678">
                            </div>
                        @endif

                        <div class="field mt-4 border-t border-indigo-100 pt-4">
                            <label class="text-indigo-900 font-semibold">Admin Note (Sent to user)</label>
                            <textarea name="admin_note" rows="2" placeholder="Any additional info or greeting..."></textarea>
                        </div>
                    </div>

                    <!-- Notification Options -->
                    <div class="flex flex-wrap gap-6 items-center bg-slate-50 p-4 rounded-xl">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="notify_email" value="1" checked class="h-4 w-4 rounded text-indigo-600">
                            <span class="text-sm font-medium text-slate-700">Notify by Email</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="notify_whatsapp" value="1" class="h-4 w-4 rounded text-green-600">
                            <span class="text-sm font-medium text-slate-700 flex items-center gap-1.5">
                                Notify by WhatsApp
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </span>
                        </label>
                    </div>

                    <button class="btn-primary w-full py-4 text-base font-bold shadow-lg shadow-indigo-100 ring-4 ring-indigo-50 leading-relaxed uppercase tracking-wider">
                        Complete & Send Details
                    </button>
                </form>
            </section>
            @else
            <section class="panel bg-green-50/30 border- green-100">
                <div class="panel-head border-b border-green-100 pb-3">
                    <h3 class="text-base font-semibold text-green-900">Fulfillment Provided</h3>
                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-tight">Ffulfilled At {{ $order->fulfilled_at?->format('Y-m-d H:i') }}</span>
                </div>
                <div class="mt-4 space-y-4">
                    @php($delivery = $order->getFulfillmentDetails())
                    @php($details = $delivery['data'] ?? [])
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($details as $d_key => $d_val)
                            <div class="p-3 bg-white rounded-lg border border-green-100">
                                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">{{ str_replace('_', ' ', $d_key) }}</p>
                                <p class="text-sm font-medium text-slate-900 break-all">{{ $d_val }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if($delivery['admin_note'] ?? null)
                    <div class="mt-4 p-4 rounded-xl bg-green-50/50 border border-green-100 border-dashed">
                        <p class="text-xs text-slate-500 font-medium mb-1">Admin Note</p>
                        <p class="text-sm text-slate-800 italic">"{{ $delivery['admin_note'] }}"</p>
                    </div>
                    @endif
                </div>
            </section>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3">
                        <h3 class="text-base font-semibold text-slate-900">Manage Status</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex gap-2 items-end">
                        @csrf
                        @method('PUT')
                        <div class="field grow">
                            <label>Status</label>
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
                    <div class="panel-head border-b border-slate-100 pb-3">
                        <h3 class="text-base font-semibold text-slate-900">Other Actions</h3>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" onsubmit="return confirm('Refund this order?')">
                            @csrf
                            <button class="btn-danger-outline">Refund Order</button>
                        </form>
                        @if($order->user?->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user->phone) }}" target="_blank" class="btn-ghost flex items-center gap-2">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                Direct WhatsApp
                            </a>
                        @else
                            <button type="button" disabled title="User has no phone number" class="btn-ghost flex items-center gap-2 opacity-30 cursor-not-allowed">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                Direct WhatsApp
                            </button>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

@endsection

