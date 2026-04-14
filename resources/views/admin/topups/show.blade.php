@extends('admin.layouts.app')
@section('title', 'Top-up Details')
@section('header', 'Top-up Details')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">Request Details</h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    @if($topup->status == 'approved') bg-green-100 text-green-700 
                    @elseif($topup->status == 'rejected') bg-red-100 text-red-700 
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ ucfirst($topup->status) }}
                </span>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div><p class="text-xs text-slate-500">User</p><p class="font-medium text-slate-900">{{ $topup->user?->name ?? 'Unknown' }}</p></div>
            <div><p class="text-xs text-slate-500">Payment Method</p><p class="font-medium text-slate-900">{{ $topup->payment_method ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Requested Amount</p><p class="font-medium text-slate-900">${{ number_format((float)$topup->amount_requested, 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Date</p><p class="font-medium text-slate-900">{{ $topup->created_at->format('Y-m-d H:i') }}</p></div>
        </div>
    </section>

    @if($topup->status === 'pending')
    <div class="grid gap-6 md:grid-cols-2">
        <section class="panel border-l-4 border-l-green-500">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900 text-green-700">Approve Request</h3></div>
            <form method="POST" action="{{ route('admin.topups.approve', $topup) }}" class="mt-4 space-y-3">
                @csrf
                <div class="field">
                    <label>Approval Note (Optional)</label>
                    <textarea name="admin_note" class="w-full" rows="3" placeholder="Add a note for the user..."></textarea>
                </div>
                <button class="btn-primary w-full">Approve & Credit Wallet</button>
            </form>
        </section>

        <section class="panel border-l-4 border-l-red-500">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900 text-red-700">Reject Request</h3></div>
            <form method="POST" action="{{ route('admin.topups.reject', $topup) }}" class="mt-4 space-y-3">
                @csrf
                <div class="field">
                    <label>Rejection Reason (Required)</label>
                    <textarea name="admin_note" class="w-full" rows="3" required placeholder="Explain why the request was rejected..."></textarea>
                </div>
                <button class="btn-danger w-full text-white bg-red-600 hover:bg-red-700 py-2 rounded-lg font-medium">Reject Request</button>
            </form>
        </section>
    </div>
    @else
    <section class="panel">
        <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Admin Response</h3></div>
        <div class="mt-4">
            <p class="text-xs text-slate-500">Note</p>
            <p class="mt-1 text-sm text-slate-800 italic">{{ $topup->admin_note ?: 'No note provided.' }}</p>
        </div>
    </section>
    @endif
</div>
@endsection

