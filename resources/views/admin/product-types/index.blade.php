@extends('admin.layouts.app')
@section('title', 'Product Types')
@section('header', 'Product Types')
@section('content')
<section class="panel">
    <div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Product Type Schemas</h2><a href="{{ route('admin.product-types.create') }}" class="btn-primary">Add Product Type</a></div>
    <form method="GET" action="{{ route('admin.product-types.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
        <div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, key, description"></div>
        <div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
        <div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.product-types.index') }}">Reset</a></div>
    </form>

    <div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Key</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    @forelse($types as $type)
        <tr>
            <td>#{{ $type->id }}</td>
            <td>{{ $type->name }}</td>
            <td>{{ $type->key }}</td>
            <td><span class="status-pill {{ $type->is_active ? 'ok':'off' }}">{{ $type->is_active ? 'Active':'Inactive' }}</span></td>
            <td class="flex gap-2"><a href="{{ route('admin.product-types.show', $type) }}" class="btn-ghost">View</a><a href="{{ route('admin.product-types.edit', $type) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.product-types.destroy', $type) }}" data-name="{{ $type->name }}">Delete</button></td>
        </tr>
    @empty
        <tr><td colspan="5">No product types found.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="mt-4">{{ $types->links() }}</div>
</section>
@endsection
