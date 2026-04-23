@extends('admin.layouts.app')
@section('title', 'Products')
@section('header', 'Products')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Product List</h2><a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a></div>

<form method="GET" action="{{ route('admin.products.index') }}" class="mt-4 grid gap-3 md:grid-cols-6">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name or slug"></div>
<div class="field"><label>Subcategory</label><select name="subcategory_id"><option value="">All</option>@foreach($subcategories as $sub)<option value="{{ $sub->id }}" @selected(($filters['subcategory_id'] ?? '') == $sub->id)>{{ $sub->name_en }}</option>@endforeach</select></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
<div class="field"><label>Type</label><select name="type"><option value="">All</option><option value="top_up" @selected(($filters['type'] ?? '')==='top_up')>Top Up</option><option value="key" @selected(($filters['type'] ?? '')==='key')>Key</option><option value="account" @selected(($filters['type'] ?? '')==='account')>Account</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.products.index') }}">Reset</a></div>
</form>

<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Type</th><th>Status</th><th>Price</th><th>Actions</th></tr></thead><tbody>
@forelse($products as $product)
<tr><td>#{{ $product->id }}</td><td>@if($product->image)<x-admin.image :path="$product->image" :alt="$product->name_en" class="h-10 w-10 rounded-lg object-cover" />@else <span class="text-xs text-slate-400">No image</span>@endif</td><td>{{ $product->name_en }}</td><td>{{ $product->resolvedProductTypeLabel() }}</td><td><span class="status-pill {{ $product->is_active ? 'ok':'off' }}">{{ $product->is_active ? 'Active':'Disabled' }}</span></td><td>${{ number_format((float)$product->selling_price,2) }}</td><td class="flex gap-2"><a href="{{ route('admin.products.show',$product) }}" class="btn-ghost">View</a><a href="{{ route('admin.products.edit',$product) }}" class="btn-ghost">Edit</a><form method="POST" action="{{ route('admin.products.duplicate', $product) }}">@csrf<button type="submit" class="btn-ghost">Duplicate</button></form><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.products.destroy', $product) }}" data-name="{{ $product->name_en }}">Delete</button></td></tr>
@empty
<tr><td colspan="7">No products found.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-4">{{ $products->links() }}</div>
</section>
@endsection
