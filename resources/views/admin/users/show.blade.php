@extends('admin.layouts.app')
@section('title', 'User Profile')
@section('header', 'User Profile')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">{{ $user->name }}</h2>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold px-3 py-1 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Active' : 'Disabled' }}
                    </button>
                </form>
                @if($user->is_vip)
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 text-amber-700Border border-amber-200">VIP</span>
                @endif
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div><p class="text-xs text-slate-500">Name</p><p class="font-medium text-slate-900">{{ $user->name }}</p></div>
            <div><p class="text-xs text-slate-500">Email</p><p class="font-medium text-slate-900">{{ $user->email }}</p></div>
            <div><p class="text-xs text-slate-500">Phone</p><p class="font-medium text-slate-900">{{ $user->phone ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Wallet Balance</p><p class="font-medium text-slate-900 text-indigo-600">${{ number_format($user->wallet?->balance ?? 0, 2) }}</p></div>
        </div>
    </section>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="panel">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Edit Profile</h3></div>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 space-y-3">
                @csrf
                @method('PUT')
                <div class="field"><label>Name</label><input name="name" value="{{ old('name', $user->name) }}" required></div>
                <div class="field"><label>Email</label><input name="email" value="{{ old('email', $user->email) }}" required></div>
                <div class="field"><label>Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}"></div>
                <div class="field">
                    <label>Preferred Language</label>
                    <select name="preferred_language">
                        <option value="en" @selected($user->preferred_language === 'en')>English</option>
                        <option value="ar" @selected($user->preferred_language === 'ar')>Arabic</option>
                    </select>
                </div>
                <button class="btn-primary w-full">Update Info</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Wallet Management</h3></div>
            <form method="POST" action="{{ route('admin.users.credit', $user) }}" class="mt-4 space-y-3">
                @csrf
                <div class="field">
                    <label>Credit/Debit Amount</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00">
                    <p class="text-xs text-slate-400 mt-1">Use positive for credit, negative for debit.</p>
                </div>
                <div class="field">
                    <label>Description</label>
                    <input name="description" required placeholder="Reason for adjustment...">
                </div>
                <button class="btn-ghost w-full">Adjust Wallet Balance</button>
            </form>
        </section>
    </div>
</div>
@endsection

