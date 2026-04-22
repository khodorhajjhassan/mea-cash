@extends('admin.layouts.app')
@section('title', 'Subcategories')
@section('header', 'Subcategories')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel">
    <div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Subcategory List</h2><a href="{{ route('admin.subcategories.create') }}" class="btn-primary">Add Subcategory</a></div>

    <form method="GET" action="{{ route('admin.subcategories.index') }}" class="mt-4 grid gap-3 md:grid-cols-5">
        <div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Subcategory or category"></div>
        <div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
        <div class="field"><label>Featured</label><select name="featured"><option value="">All</option><option value="yes" @selected(($filters['featured'] ?? '')==='yes')>Featured</option><option value="no" @selected(($filters['featured'] ?? '')==='no')>Not Featured</option></select></div>
        <div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.subcategories.index') }}">Reset</a></div>
    </form>

    <div class="table-wrap mt-4">
        <table class="admin-table"><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Template</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @forelse($subcategories as $subcategory)
            <tr>
                <td>#{{ $subcategory->id }}</td>
                <td>@if($subcategory->image)<x-admin.image :path="$subcategory->image" :alt="$subcategory->name_en" class="h-10 w-10 rounded-lg object-cover" />@else <span class="text-xs text-slate-400">No image</span>@endif</td>
                <td>{{ $subcategory->name_en }}</td>
                <td>{{ $subcategory->category?->name_en ?? '-' }}</td>
                <td>{{ $subcategory->productTypeDefinition?->name ?? '-' }}</td>
                <td><span class="status-pill {{ $subcategory->is_featured ? 'ok':'off' }}">{{ $subcategory->is_featured ? 'Yes':'No' }}</span></td>
                <td><span class="status-pill {{ $subcategory->is_active ? 'ok':'off' }}">{{ $subcategory->is_active ? 'Active':'Disabled' }}</span></td>
                <td class="flex gap-2"><a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn-ghost">View</a><a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.subcategories.destroy', $subcategory) }}" data-name="{{ $subcategory->name_en }}">Delete</button></td>
            </tr>
        @empty
            <tr><td colspan="8">No subcategories found.</td></tr>
        @endforelse
        </tbody></table>
    </div>
    <div class="mt-4">{{ $subcategories->links() }}</div>
</section>
@endsection
