@extends('admin.layouts.app')
@section('title','Contact Messages')
@section('header','Contact Messages')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.contact.index') }}" class="mb-4 grid gap-3 md:grid-cols-3">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email, subject, message"></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.contact.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Actions</th></tr></thead><tbody>
@forelse($messages as $msg)
<tr><td>#{{ $msg->id }}</td><td>{{ $msg->name }}</td><td>{{ $msg->email }}</td><td>{{ $msg->subject ?: '-' }}</td><td class="flex gap-2"><a href="{{ route('admin.contact.show',$msg) }}" class="btn-ghost">Read</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.contact.destroy', $msg) }}" data-name="Message #{{ $msg->id }}">Delete</button></td></tr>
@empty <tr><td colspan="5">No messages.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $messages->links() }}</div></section>
@endsection
