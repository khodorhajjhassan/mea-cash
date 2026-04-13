@extends('admin.layouts.app')
@section('title','Users')
@section('header','All Users')
@section('content')
<section class="panel">
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or phone"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.users.index') }}">Reset</a></div>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Active</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>#{{ $user->id }}</td><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->is_active ? 'Yes':'No' }}</td><td class="flex gap-2"><a href="{{ route('admin.users.show',$user) }}" class="btn-ghost">View</a><form method="POST" action="{{ route('admin.users.toggle',$user) }}">@csrf<button class="btn-danger-outline">Toggle</button></form></td></tr>
@empty <tr><td colspan="5">No users.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $users->links() }}</div></section>
@endsection
