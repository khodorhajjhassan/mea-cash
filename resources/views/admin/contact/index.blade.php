@extends('admin.layouts.app')
@section('title','Contact Messages')
@section('header','Contact Messages')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Actions</th></tr></thead><tbody>
@forelse($messages as $msg)
<tr><td>#{{ $msg->id }}</td><td>{{ $msg->name }}</td><td>{{ $msg->email }}</td><td>{{ $msg->subject ?: '-' }}</td><td class="flex gap-2"><a href="{{ route('admin.contact.show',$msg) }}" class="btn-ghost">Read</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.contact.destroy', $msg) }}" data-name="Message #{{ $msg->id }}">Delete</button></td></tr>
@empty <tr><td colspan="5">No messages.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $messages->links() }}</div></section>
@endsection
