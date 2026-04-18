@extends('admin.layouts.app')

@section('title', 'Categories')
@section('header', 'Categories')

@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">Category List</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
    </div>

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
        <div class="field md:col-span-2">
            <label>Search</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name or slug">
        </div>
        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary" type="submit">Filter</button>
            <a class="btn-ghost" href="{{ route('admin.categories.index') }}">Reset</a>
        </div>
    </form>

    <div class="table-wrap mt-4">
        <table class="admin-table">
            <thead>
            <tr><th width="40"></th><th>ID</th><th>Image</th><th>Name</th><th>Slug</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <tr data-id="{{ $category->id }}">
                    <td class="drag-handle cursor-move text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                    </td>
                    <td>#{{ $category->id }}</td>
                    <td>
                        @if($category->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($category->image) }}" alt="{{ $category->name_en }}" class="h-10 w-10 rounded-lg object-cover">
                        @else
                            <span class="text-xs text-slate-400">No image</span>
                        @endif
                    </td>
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
                <tr><td colspan="6">No categories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.querySelector('.admin-table tbody');
        if (!el) return;

        Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                const orders = [];
                el.querySelectorAll('tr').forEach((tr, index) => {
                    orders.push({
                        id: tr.dataset.id,
                        sort_order: index + 1
                    });
                });

                fetch('{{ route("admin.categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ orders: orders })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Optional: Show a subtle success toast
                    }
                })
                .catch(err => console.error('Reorder failed:', err));
            }
        });
    });
</script>
@endpush
@endsection
