@extends('admin.layouts.app')
@section('title','Feedback')
@section('header','Feedback')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Order</th><th>Rating</th><th>Actions</th></tr></thead><tbody>
@forelse($feedbacks as $feedback)
<tr><td>#{{ $feedback->id }}</td><td>{{ $feedback->user?->name ?? '-' }}</td><td>{{ $feedback->order?->order_number ?? '-' }}</td><td>{{ $feedback->rating }}/5</td><td class="flex gap-2"><a href="{{ route('admin.feedback.show',$feedback) }}" class="btn-ghost">View</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.feedback.destroy', $feedback) }}" data-name="Feedback #{{ $feedback->id }}">Delete</button></td></tr>
@empty <tr><td colspan="5">No feedback entries.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $feedbacks->links() }}</div></section>
@endsection
