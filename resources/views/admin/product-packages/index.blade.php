@extends('admin.layouts.app')
@section('title', 'Product Packages')
@section('header', 'Product Packages')
@section('content')
<section class="panel"><div class="panel-head"><h2 class="text-lg font-semibold text-slate-900">Package List</h2><a href="{{ route('admin.product-packages.create') }}" class="btn-primary">Add Package</a></div>
<div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Product</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($packages as $package)
<tr><td>#{{ $package->id }}</td><td>{{ $package->name_en }}</td><td>{{ $package->product?->name_en ?? '-' }}</td><td>{{ $package->amount }}</td><td><span class="status-pill {{ $package->is_available ? 'ok':'off' }}">{{ $package->is_available ? 'Available':'Unavailable' }}</span></td><td class="flex gap-2"><a href="{{ route('admin.product-packages.show', $package) }}" class="btn-ghost">View</a><a href="{{ route('admin.product-packages.edit', $package) }}" class="btn-ghost">Edit</a><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.product-packages.destroy', $package) }}" data-name="{{ $package->name_en }}">Delete</button></td></tr>
@empty
<tr><td colspan="6">No packages found.</td></tr>
@endforelse
</tbody></table></div><div class="mt-4">{{ $packages->links() }}</div></section>
@endsection
