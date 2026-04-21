@extends('admin.layouts.app')
@section('title', __('admin.sidebar.orders_group'))
@section('header', __('admin.sidebar.all_orders'))
@section('content')

@if(session('whatsapp_url'))
<div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-2xl flex items-center justify-between shadow-sm animate-pulse">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-green-900 leading-tight">{{ $order->status === App\Enums\OrderStatus::Refunded ? 'Order Refunded!' : 'Order Fulfilled!' }}</p>
            <p class="text-xs text-green-700">Would you like to notify the customer via WhatsApp now?</p>
        </div>
    </div>
    <a href="{{ session('whatsapp_url') }}" target="_blank" class="bg-green-600 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-green-700 transition-all flex items-center gap-2">
        Click to Notify
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
</div>
@endif

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
                @php
                $statusColors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'reported' => 'bg-rose-100 text-rose-700',
                    'failed' => 'bg-rose-100 text-rose-700',
                    'refunded' => 'bg-rose-100 text-rose-700',
                ];
                $colorClass = $statusColors[$order->status->value] ?? 'bg-slate-100 text-slate-500';
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider {{ $colorClass }}">
                    {{ $order->status->value }}
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
            @php($delivery = $order->getFulfillmentDetails())
            @php($details = $delivery['data'] ?? [])
            @if($order->status !== App\Enums\OrderStatus::Refunded)
            <section class="panel border-2 border-indigo-50 leading-relaxed shadow-sm">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-indigo-900">{{ $order->status === App\Enums\OrderStatus::Completed ? 'Update Fulfillment' : 'Order Fulfillment' }}</h3>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded uppercase font-bold tracking-tight">
                        {{ str_replace('_', ' ', $order->product?->product_type?->value ?? 'Manual') }}
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
                                <textarea name="keys" rows="5" placeholder="Enter one or multiple keys here..." class="w-full js-fulfillment-editor">{!! $details['keys'] ?? '' !!}</textarea>
                                <p class="text-xs text-slate-500 mt-1">These will be delivered to the user exactly as formatted here.</p>
                            </div>
                        @elseif($order->isTypeAccount())
                            <input type="hidden" name="fulfillment_type" value="account">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="field"><label class="text-indigo-900 font-semibold">Account Email/User</label><input type="text" name="account_user" value="{{ $details['user'] ?? '' }}" required></div>
                                <div class="field"><label class="text-indigo-900 font-semibold">Account Password</label><input type="text" name="account_pass" value="{{ $details['pass'] ?? '' }}" required></div>
                                <div class="field md:col-span-2"><label class="text-indigo-900 font-semibold">Login Link (Optional)</label><input type="url" name="account_link" value="{{ $details['link'] ?? '' }}" placeholder="https://..."></div>
                                <div class="field md:col-span-2"><label class="text-indigo-900 font-semibold">Account Details / Instructions</label><textarea name="account_details" rows="4" class="js-fulfillment-editor">{!! $details['account_details'] ?? '' !!}</textarea></div>
                            </div>
                        @else
                            <input type="hidden" name="fulfillment_type" value="topup">
                            <div class="space-y-4">
                                <div class="field">
                                    <label class="text-indigo-900 font-semibold">Transaction Reference / ID</label>
                                    <input type="text" name="transaction_id" value="{{ $details['transaction_id'] ?? '' }}" placeholder="e.g. TXN12345678">
                                    <p class="text-[10px] text-slate-500 mt-1 uppercase">For game IDs or external payment proofs.</p>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="field"><label class="text-indigo-900 font-semibold text-xs">Account Email/User (Optional)</label><input type="text" name="account_user" value="{{ $details['user'] ?? '' }}" class="text-xs"></div>
                                    <div class="field"><label class="text-indigo-900 font-semibold text-xs">Account Password (Optional)</label><input type="text" name="account_pass" value="{{ $details['pass'] ?? '' }}" class="text-xs"></div>
                                </div>
                                <div class="field">
                                    <label class="text-indigo-900 font-semibold text-xs">Account Details / Instructions (Optional)</label>
                                    <textarea name="account_details" rows="3" class="text-xs js-fulfillment-editor">{!! $details['account_details'] ?? '' !!}</textarea>
                                </div>
                                <div class="field">
                                    <label class="text-indigo-900 font-semibold text-xs">Additional Digital Keys / Codes (Optional)</label>
                                    <textarea name="keys" rows="2" placeholder="Paste keys here if any..." class="text-xs js-fulfillment-editor">{!! $details['keys'] ?? '' !!}</textarea>
                                </div>
                            </div>
                        @endif

                        <div class="field mt-4 border-t border-indigo-100 pt-4">
                            <label class="text-indigo-900 font-semibold">Fulfillment Message / Admin Note</label>
                            <textarea name="admin_note" rows="3" placeholder="This message will be sent to the user..." class="js-fulfillment-editor">{!! $delivery['admin_note'] ?? '' !!}</textarea>
                            <p class="text-[10px] text-slate-500 mt-1 uppercase">Include detailed instructions or a thank you message here.</p>
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
                        {{ $order->status === App\Enums\OrderStatus::Completed ? 'Update & Send Details' : 'Complete & Send Details' }}
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
                            @continue(empty($d_val))
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

                    @if($order->status === App\Enums\OrderStatus::Refunded)
                    <div class="mt-6 p-6 bg-rose-50 border border-rose-100 rounded-3xl">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1-4h1m4 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Refund Information</h4>
                                <p class="text-[10px] text-rose-500 font-bold uppercase tracking-wider">Order Cancelled & Refunded</p>
                            </div>
                        </div>
                        @if($order->refund_notes)
                            <div class="bg-white/50 border border-rose-100/50 p-4 rounded-xl">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Reason / Note</p>
                                <p class="text-sm text-slate-800 italic">"{{ $order->refund_notes }}"</p>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </section>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                @if($order->status !== App\Enums\OrderStatus::Completed && $order->status !== App\Enums\OrderStatus::Refunded)
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3">
                        <h3 class="text-base font-semibold text-slate-900">Manage Status</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex gap-2 items-end">
                        @csrf
                        <div class="field grow">
                            <label>Status</label>
                            <select name="status" class="w-full">
                                @foreach(['pending', 'processing', 'completed', 'reported', 'failed', 'refunded'] as $status)
                                    <option value="{{ $status }}" @selected($order->status->value === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn-primary">Save</button>
                    </form>
                </section>
                @endif

                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3">
                        <h3 class="text-base font-semibold text-slate-900">Other Actions</h3>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($order->status !== App\Enums\OrderStatus::Refunded)
                        <button type="button" onclick="document.getElementById('refundModal').classList.remove('hidden')" class="btn-danger-outline">Refund Order</button>
                        @endif
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

{{-- Refund Modal --}}
<div id="refundModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="p-8 border-b border-slate-50 text-center">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Refund Order</h3>
            <p class="text-slate-500 text-sm mt-1">This will return <span class="font-bold text-rose-500">${{ number_format($order->total_price, 2) }}</span> to the user's wallet.</p>
        </div>

        <form action="{{ route('admin.orders.refund', $order) }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="field">
                <label class="text-slate-900 font-bold text-sm">Reason for Refund (Optional)</label>
                <textarea name="notes" rows="3" class="p-4 w-full bg-slate-50 border-slate-100 rounded-xl focus:ring-rose-500/20 focus:border-rose-500/50" placeholder="Why are you refunding this order?"></textarea>
            </div>

            <div class="grid gap-4 p-4 bg-slate-50 rounded-xl">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="notify_email" value="1" checked class="h-4 w-4 rounded text-rose-600">
                    <span class="text-sm font-medium text-slate-700">Notify User by Email</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="notify_whatsapp" value="1" class="h-4 w-4 rounded text-green-600">
                    <span class="text-sm font-medium text-slate-700">Notify User by WhatsApp</span>
                </label>
            </div>
            
            <div class="flex gap-4 pt-2">
                <button type="button" onclick="document.getElementById('refundModal').classList.add('hidden')" class="btn-ghost flex-1 py-4 text-slate-400 font-bold uppercase tracking-wider text-xs">Cancel</button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white flex-[2] py-4 rounded-xl shadow-lg shadow-rose-100 font-bold uppercase tracking-widest text-xs transition-all active:scale-[0.98]">Confirm Refund</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    const fulfillmentEditors = [];
    document.querySelectorAll('.js-fulfillment-editor').forEach((textarea) => {
        ClassicEditor.create(textarea, {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            }
        }).then((editor) => fulfillmentEditors.push(editor)).catch((error) => console.error(error));
    });

    document.querySelectorAll('form[action*="/fulfill"]').forEach((form) => {
        form.addEventListener('submit', () => {
            fulfillmentEditors.forEach((editor) => editor.updateSourceElement());
        });
    });
</script>
<style>
    .ck-editor__editable {
        min-height: 160px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        background-color: #f8fafc !important;
    }
    .ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        border-bottom: 0 !important;
        background-color: #ffffff !important;
    }
</style>
@endpush

@endsection
