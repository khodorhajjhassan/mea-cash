@extends('admin.layouts.app')
@section('title','Feedback')
@section('header','Feedback')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.feedback.index') }}" class="mb-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="User, order, comment"></div>
<div class="field"><label>Rating</label><select name="rating"><option value="">All</option>@for($i=1;$i<=5;$i++)<option value="{{ $i }}" @selected(($filters['rating'] ?? '')==(string)$i)>{{ $i }}</option>@endfor</select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.feedback.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Order</th><th>Rating</th><th>Actions</th></tr></thead><tbody>
@forelse($feedbacks as $feedback)
<tr><td>#{{ $feedback->id }}</td><td>{{ $feedback->user?->name ?? '-' }}</td><td>{{ $feedback->order?->order_number ?? '-' }}</td><td>{{ $feedback->rating }}/5</td><td class="flex gap-2"><a href="{{ route('admin.feedback.show',$feedback) }}" class="btn-ghost">View</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.feedback.destroy', $feedback) }}" data-name="Feedback #{{ $feedback->id }}">Delete</button></td></tr>
@empty <tr><td colspan="5">No feedback entries.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $feedbacks->links() }}</div></section>
@endsection
