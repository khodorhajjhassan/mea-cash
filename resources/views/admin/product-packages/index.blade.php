@extends('admin.layouts.app')
@section('title', 'Product Packages')
@section('header', 'Product Packages')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Package List</h2><a href="{{ route('admin.product-packages.create') }}" class="btn-primary">Add Package</a></div>
<form method="GET" action="{{ route('admin.product-packages.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Package or product"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="available" @selected(($filters['status'] ?? '')==='available')>Available</option><option value="unavailable" @selected(($filters['status'] ?? '')==='unavailable')>Unavailable</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.product-packages.index') }}">Reset</a></div>
</form>
<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Product</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($packages as $package)
<tr><td>#{{ $package->id }}</td><td>@if($package->image)<img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($package->image) }}" alt="{{ $package->name_en }}" class="h-10 w-10 rounded-lg object-cover">@else <span class="text-xs text-slate-400">No image</span>@endif</td><td>{{ $package->name_en }}</td><td>{{ $package->product?->name_en ?? '-' }}</td><td>{{ $package->amount }}</td><td><span class="status-pill {{ $package->is_available ? 'ok':'off' }}">{{ $package->is_available ? 'Available':'Unavailable' }}</span></td><td class="flex gap-2"><a href="{{ route('admin.product-packages.show', $package) }}" class="btn-ghost">View</a><a href="{{ route('admin.product-packages.edit', $package) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.product-packages.destroy', $package) }}" data-name="{{ $package->name_en }}">Delete</button></td></tr>
@empty
<tr><td colspan="7">No packages found.</td></tr>
@endforelse
</tbody></table></div><div class="mt-4">{{ $packages->links() }}</div></section>
@endsection
