@extends('admin.layouts.app')
@section('title','Contact Messages')
@section('header','Contact Messages')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.contact.index') }}" class="mb-4 grid gap-3 md:grid-cols-5">
<div class="field md:col-span-1"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search..."></div>
<div class="field"><label>From</label><input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"></div>
<div class="field"><label>To</label><input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"></div>
<div class="flex items-end gap-2 md:col-span-2"><button class="btn-primary flex-1" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.contact.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($messages as $msg)
<tr class="{{ !$msg->is_read ? 'bg-blue-50/50' : '' }}"><td>#{{ $msg->id }}</td><td class="{{ !$msg->is_read ? 'font-bold text-slate-900' : '' }}">{{ $msg->name }}</td><td>{{ $msg->email }}</td><td class="{{ !$msg->is_read ? 'font-bold text-slate-900' : '' }}">{{ $msg->subject ?: '-' }}</td><td>@if(!$msg->is_read)<span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-blue-100 text-blue-700">Unread</span>@else<span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-slate-100 text-slate-500">Read</span>@endif</td><td class="flex gap-2"><a href="{{ route('admin.contact.show',$msg) }}" class="btn-ghost">Read</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.contact.destroy', $msg) }}" data-name="Message #{{ $msg->id }}">Delete</button></td></tr>
@empty <tr><td colspan="6">No messages.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $messages->links() }}</div></section>
@endsection
