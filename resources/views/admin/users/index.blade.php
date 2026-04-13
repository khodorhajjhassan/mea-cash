@extends('admin.layouts.app')
@section('title','Users')
@section('header','All Users')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Active</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>#{{ $user->id }}</td><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->is_active ? 'Yes':'No' }}</td><td class="flex gap-2"><a href="{{ route('admin.users.show',$user) }}" class="btn-ghost">View</a><form method="POST" action="{{ route('admin.users.toggle',$user) }}">@csrf<button class="btn-danger-outline">Toggle</button></form></td></tr>
@empty <tr><td colspan="5">No users.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $users->links() }}</div></section>
@endsection
