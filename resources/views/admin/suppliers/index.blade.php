@extends('admin.layouts.app')
@section('title','Suppliers')
@section('header','Suppliers')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold">Supplier List</h2><a href="{{ route('admin.suppliers.create') }}" class="btn-primary">Add Supplier</a></div><div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Active</th><th>Actions</th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td>#{{ $supplier->id }}</td><td>{{ $supplier->name }}</td><td>{{ $supplier->contact_name ?: '-' }}</td><td>{{ $supplier->is_active ? 'Yes':'No' }}</td><td class="flex gap-2"><a href="{{ route('admin.suppliers.edit',$supplier) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.suppliers.destroy', $supplier) }}" data-name="{{ $supplier->name }}">Delete</button></td></tr>@empty<tr><td colspan="5">No suppliers found.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $suppliers->links() }}</div></section>
@endsection
