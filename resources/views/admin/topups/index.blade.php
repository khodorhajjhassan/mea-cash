@extends('admin.layouts.app')
@section('title','Top-ups')
@section('header','Top-up Requests')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.topups.index') }}" class="mb-4 grid gap-3 md:grid-cols-5">
<div class="field md:col-span-1"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="User or payment method"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
<div class="field"><label>From</label><input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"></div>
<div class="field"><label>To</label><input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.topups.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($topups as $topup)
    @php
        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-rose-100 text-rose-700',
        ];
        $colorClass = $statusColors[$topup->status] ?? 'bg-slate-100 text-slate-600';
    @endphp
    <tr><td>#{{ $topup->id }}</td><td>{{ $topup->user?->name ?? '-' }}</td><td>${{ number_format((float)$topup->amount_requested,2) }}</td><td><span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">{{ $topup->status }}</span></td><td class="flex gap-2"><a class="btn-ghost" href="{{ route('admin.topups.show',$topup) }}">View</a></td></tr>
@empty <tr><td colspan="5">No topup requests.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $topups->links() }}</div></section>
@endsection
