@extends('admin.layouts.app')
@section('title', 'Products')
@section('header', 'Products')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Product List</h2><a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a></div>
<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Price</th><th>Actions</th></tr></thead><tbody>
@forelse($products as $product)
<tr><td>#{{ $product->id }}</td><td>{{ $product->name_en }}</td><td>{{ str_replace('_', ' ', $product->product_type) }}</td><td><span class="status-pill {{ $product->is_active ? 'ok':'off' }}">{{ $product->is_active ? 'Active':'Disabled' }}</span></td><td>${{ number_format((float)$product->selling_price,2) }}</td><td class="flex gap-2"><a href="{{ route('admin.products.show',$product) }}" class="btn-ghost">View</a><a href="{{ route('admin.products.edit',$product) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.products.destroy', $product) }}" data-name="{{ $product->name_en }}">Delete</button></td></tr>
@empty
<tr><td colspan="6">No products found.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-4">{{ $products->links() }}</div>
</section>
@endsection
