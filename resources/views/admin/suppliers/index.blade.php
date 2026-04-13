@extends('admin.layouts.app')
@section('title','Suppliers')
@section('header','Suppliers')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold">Supplier List</h2><a href="{{ route('admin.suppliers.create') }}" class="btn-primary">Add Supplier</a></div>
<form method="GET" action="{{ route('admin.suppliers.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, contact, email"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.suppliers.index') }}">Reset</a></div>
</form>
<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Active</th><th>Actions</th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td>#{{ $supplier->id }}</td><td>{{ $supplier->name }}</td><td>{{ $supplier->contact_name ?: '-' }}</td><td>{{ $supplier->is_active ? 'Yes':'No' }}</td><td class="flex gap-2"><a href="{{ route('admin.suppliers.edit',$supplier) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.suppliers.destroy', $supplier) }}" data-name="{{ $supplier->name }}">Delete</button></td></tr>@empty<tr><td colspan="5">No suppliers found.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $suppliers->links() }}</div></section>
@endsection
