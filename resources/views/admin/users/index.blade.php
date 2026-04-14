@extends('admin.layouts.app')
@section('title','Users')
@section('header','All Users')
@section('content')
<section class="panel">
    <div class="panel-head border-b border-slate-100 pb-3 mb-4 flex justify-between items-center">
        <h2 class="text-lg font-bold text-slate-800 tracking-tight">System Users</h2>
        <a href="{{ route('admin.users.create') }}" class="btn-primary text-xs uppercase font-bold tracking-widest shadow-lg shadow-indigo-100">Create New User</a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or phone"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.users.index') }}">Reset</a></div>
</form>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Active':'Inactive' }}
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.users.show',$user) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wider">Dashboard</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
<div class="mt-4">{{ $users->links() }}</div></section>
@endsection
