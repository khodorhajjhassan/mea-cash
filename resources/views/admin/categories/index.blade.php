@extends('admin.layouts.app')

@section('title', 'Categories')
@section('header', 'Categories')

@section('content')
<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">Category List</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
    </div>

    <div class="table-wrap mt-4">
        <table class="admin-table">
            <thead>
            <tr><th>ID</th><th>Name</th><th>Slug</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>#{{ $category->id }}</td>
                    <td>{{ $category->name_en }}</td>
                    <td>{{ $category->slug }}</td>
                    <td><span class="status-pill {{ $category->is_active ? 'ok' : 'off' }}">{{ $category->is_active ? 'Active' : 'Disabled' }}</span></td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.categories.show', $category) }}" class="btn-ghost">View</a>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn-ghost">Edit</a>
                        <button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.categories.destroy', $category) }}" data-name="{{ $category->name_en }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No categories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</section>
@endsection
