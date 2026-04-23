@extends('admin.layouts.app')
 
@section('title', 'Hero Banners')
@section('header', 'Hero Banners')
 
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">Banner List</h2>
        <a href="{{ route('admin.banners.create') }}" class="btn-primary">Add Banner</a>
    </div>
 
    <div class="table-wrap mt-6">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Sort</th>
                <th>Image</th>
                <th>Title (EN/AR)</th>
                <th>Position</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($banners as $banner)
                <tr>
                    <td>#{{ $banner->sort_order }}</td>
                    <td>
                        <x-admin.image :path="$banner->image_path" alt="Banner" class="h-12 w-24 rounded-lg object-cover" />
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $banner->title_en ?: 'No Title' }}</div>
                        <div class="text-xs text-slate-500">{{ $banner->title_ar }}</div>
                    </td>
                    <td>
                        <span class="capitalize text-slate-700">{{ $banner->position }}</span>
                    </td>
                    <td>
                        <span class="status-pill {{ $banner->is_active ? 'ok' : 'off' }}">
                            {{ $banner->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn-ghost">Edit</a>
                        <button type="button" class="btn-danger-outline js-delete-button" 
                                data-action="{{ route('admin.banners.destroy', $banner) }}" 
                                data-name="Banner #{{ $banner->id }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-8">No banners found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
 
    <div class="mt-4">{{ $banners->links() }}</div>
</section>
@endsection
