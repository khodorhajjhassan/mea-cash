@extends('admin.layouts.app')
@section('title','Feedback')
@section('header','Feedback')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.feedback.index') }}" class="mb-4 grid gap-3 md:grid-cols-6">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="User, order, comment"></div>
<div class="field"><label>Rating</label><select name="rating"><option value="">All</option>@for($i=1;$i<=5;$i++)<option value="{{ $i }}" @selected(($filters['rating'] ?? '')==(string)$i)>{{ $i }}</option>@endfor</select></div>
<div class="field"><label>Type</label><select name="type"><option value="">All</option><option value="feedback" @selected(($filters['type'] ?? '')==='feedback')>Feedback</option><option value="report" @selected(($filters['type'] ?? '')==='report')>Reports</option></select></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option>@foreach(['open','reviewing','resolved','refunded'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.feedback.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Order</th><th>Type</th><th>Rating/Status</th><th>Featured</th><th>Actions</th></tr></thead><tbody>
@forelse($feedbacks as $feedback)
<tr>
    <td>#{{ $feedback->id }}</td>
    <td>
        <div class="font-bold">{{ $feedback->user?->name ?? '-' }}</div>
        <div class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $feedback->user?->completed_orders_count ?? 0 }} Completed Orders</div>
    </td>
    <td>{{ $feedback->order?->order_number ?? '-' }}</td>
    <td><span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $feedback->type === 'report' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ $feedback->type ?? 'feedback' }}</span></td>
    <td>{{ $feedback->type === 'report' ? ucfirst($feedback->status ?? 'open') : $feedback->rating.'/5' }}</td>
    <td>
        @if($feedback->type !== 'report')
        <form method="POST" action="{{ route('admin.feedback.toggle-featured', $feedback) }}">
            @csrf
            <button type="submit" class="flex items-center gap-1 {{ $feedback->show_on_homepage ? 'text-amber-500' : 'text-slate-300' }} hover:scale-110 transition">
                <span class="material-symbols-outlined">{{ $feedback->show_on_homepage ? 'grade' : 'star' }}</span>
                <span class="text-[10px] font-bold uppercase">{{ $feedback->show_on_homepage ? 'Featured' : 'Feature' }}</span>
            </button>
        </form>
        @else
        -
        @endif
    </td>
    <td class="flex gap-2">
        <a href="{{ route('admin.feedback.show',$feedback) }}" class="btn-ghost">View</a>
        <button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.feedback.destroy', $feedback) }}" data-name="Feedback #{{ $feedback->id }}">Delete</button>
    </td>
</tr>
@empty <tr><td colspan="7">No feedback entries.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $feedbacks->links() }}</div></section>
@endsection
