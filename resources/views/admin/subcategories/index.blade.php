@extends('admin.layouts.app')
@section('title', 'Subcategories')
@section('header', 'Subcategories')
@section('content')
<section class="panel">
    <div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Subcategory List</h2><a href="{{ route('admin.subcategories.create') }}" class="btn-primary">Add Subcategory</a></div>
    <div class="table-wrap mt-4">
        <table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @forelse($subcategories as $subcategory)
            <tr>
                <td>#{{ $subcategory->id }}</td>
                <td>{{ $subcategory->name_en }}</td>
                <td>{{ $subcategory->category?->name_en ?? '-' }}</td>
                <td><span class="status-pill {{ $subcategory->is_active ? 'ok':'off' }}">{{ $subcategory->is_active ? 'Active':'Disabled' }}</span></td>
                <td class="flex gap-2"><a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn-ghost">View</a><a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.subcategories.destroy', $subcategory) }}" data-name="{{ $subcategory->name_en }}">Delete</button></td>
            </tr>
        @empty
            <tr><td colspan="5">No subcategories found.</td></tr>
        @endforelse
        </tbody></table>
    </div>
    <div class="mt-4">{{ $subcategories->links() }}</div>
</section>
@endsection
