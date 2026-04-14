@extends('admin.layouts.app')
@section('title', 'Top-up Details')
@section('header', 'Top-up Details')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3">
            <h2 class="text-lg font-semibold text-slate-900">Request #{{ $topup->id }}</h2>
            <div class="flex gap-2">
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-rose-100 text-rose-700',
                    ];
                    $colorClass = $statusColors[$topup->status] ?? 'bg-slate-100 text-slate-500';
                @endphp
                <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full {{ $colorClass }}">
                    {{ $topup->status }}
                </span>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div><p class="text-xs text-slate-500">User</p><p class="font-medium text-slate-900">{{ $topup->user?->name ?? 'Unknown' }}</p></div>
            <div><p class="text-xs text-slate-500">Payment Method</p><p class="font-medium text-slate-900 uppercase">{{ $topup->payment_method ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Requested Amount</p><p class="font-semibold text-indigo-600">${{ number_format((float)$topup->amount_requested, 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Submitted At</p><p class="font-medium text-slate-900">{{ $topup->created_at->format('Y-m-d H:i') }}</p></div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Receipt View -->
        <div class="lg:col-span-1">
            <section class="panel h-full">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900">Payment Receipt</h3>
                </div>
                <div class="mt-4">
                    @if($topup->receipt_image_path)
                        @php($imageUrl = \Illuminate\Support\Facades\Storage::disk('private')->temporaryUrl($topup->receipt_image_path, now()->addMinutes(10)))
                        <a href="{{ $imageUrl }}" target="_blank" class="block group">
                            <img src="{{ $imageUrl }}" class="w-full rounded-lg shadow-sm border border-slate-200 group-hover:opacity-90 transition-opacity">
                            <p class="mt-2 text-center text-xs text-slate-400 font-medium group-hover:text-indigo-500 underline">View Full Image (Secure Link)</p>
                        </a>
                    @else
                        <div class="py-20 text-center bg-slate-50 rounded-lg border-2 border-dashed border-slate-200 text-slate-400 italic">No receipt image uploaded.</div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Action Center -->
        <div class="lg:col-span-2 space-y-6">
            @if($topup->status === 'pending')
            <section class="panel border-2 border-indigo-50 shadow-sm">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900">Process Request</h3>
                </div>
                <div class="mt-6 flex flex-wrap gap-4">
                    <button type="button" onclick="document.getElementById('approveModal').classList.remove('hidden')" class="btn-primary py-3 px-8 text-base font-bold shadow-lg shadow-indigo-100">
                        Approve & Credit Wallet
                    </button>
                    
                    <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="btn-danger-outline py-3 px-8 text-base font-semibold">
                        Reject Request
                    </button>
                </div>
            </section>
            @else
            <section class="panel @if($topup->status == 'approved') bg-green-50/30 border-green-100 @else bg-red-50/30 border-red-100 @endif">
                <div class="panel-head border-b @if($topup->status == 'approved') border-green-100 @else border-red-100 @endif pb-3">
                    <h3 class="text-base font-semibold @if($topup->status == 'approved') text-green-900 @else text-red-900 @endif">Admin Decision</h3>
                    <span class="text-[10px] font-bold text-slate-400">Processed by {{ $topup->processor?->name ?? 'Admin' }} on {{ $topup->processed_at?->format('Y-m-d H:i') }}</span>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-slate-500 font-medium mb-1">Note to User</p>
                    <p class="text-sm text-slate-800 italic leading-relaxed">"{{ $topup->admin_note ?: 'No note provided.' }}"</p>
                </div>
            </section>
            @endif

            <section class="panel">
                <div class="panel-head border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900 text-right">Quick Contact</h3>
                </div>
                <div class="mt-4 flex justify-end gap-2 text-right">
                    @if($topup->user?->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $topup->user->phone) }}" target="_blank" class="btn-ghost flex items-center gap-2">
                            <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.623 1.432h.005c6.551 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            WhatsApp the User
                        </a>
                    @endif
                    <a href="mailto:{{ $topup->user?->email }}" class="btn-ghost flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Send Email
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Modal Approval -->
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden ring-1 ring-slate-200">
        <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">Approve Top-up</h3>
                <p class="text-indigo-100 text-xs">Verify amount and refill user wallet</p>
            </div>
            <button onclick="document.getElementById('approveModal').classList.add('hidden')" class="p-2 hover:bg-white/10 rounded-full transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('admin.topups.approve', $topup) }}" class="p-8 space-y-6">
            @csrf
            
            <div class="field">
                <label class="text-slate-900 font-bold">Final Amount to Credit ($)</label>
                <input type="number" name="amount_credited" step="0.01" value="{{ (float)$topup->amount_requested }}" class="w-full text-lg font-bold text-indigo-700 bg-indigo-50 border-indigo-200 focus:ring-indigo-500" required>
                <p class="text-xs text-slate-400 mt-1">Requested: ${{ number_format((float)$topup->amount_requested, 2) }}</p>
            </div>

            <div class="field">
                <label class="text-slate-900 font-bold">Confirmation Note</label>
                <textarea name="admin_note" rows="3" placeholder="Hello! Your top-up has been processed."></textarea>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_email" value="1" checked class="h-4 w-4 rounded text-indigo-600">
                    <span class="text-sm font-medium text-slate-700">Notify user by Email</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_whatsapp" value="1" class="h-4 w-4 rounded text-green-600">
                    <span class="text-sm font-medium text-slate-700">Notify user by WhatsApp</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="btn-ghost flex-1 py-3">Cancel</button>
                <button type="submit" class="btn-primary flex-2 py-3 font-bold uppercase tracking-wider">Confirm Approval</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden ring-1 ring-slate-200">
        <div class="bg-red-600 p-6 text-white flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">Reject Request</h3>
                <p class="text-red-100 text-xs">This action will notify the user of rejection</p>
            </div>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="p-2 hover:bg-white/10 rounded-full transition-colors"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('admin.topups.reject', $topup) }}" class="p-8 space-y-6">
            @csrf
            <div class="field">
                <label class="text-slate-900 font-bold">Rejection Reason</label>
                <textarea name="admin_note" rows="4" required placeholder="Evidence not clear, mismatch in amount, etc..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="btn-ghost flex-1 py-3">Cancel</button>
                <button type="submit" class="btn-danger w-full text-white bg-red-600 hover:bg-red-700 py-3 rounded-lg font-bold uppercase">Reject Permanently</button>
            </div>
        </form>
    </div>
</div>

<style>
    .flex-2 { flex: 2; }
</style>
@endsection

