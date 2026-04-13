@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <div class="stat-card"><p>Categories</p><h3>{{ $stats['categories'] }}</h3></div>
    <div class="stat-card"><p>Subcategories</p><h3>{{ $stats['subcategories'] }}</h3></div>
    <div class="stat-card"><p>Products</p><h3>{{ $stats['products'] }}</h3></div>
    <div class="stat-card"><p>Orders</p><h3>{{ $stats['orders'] }}</h3></div>
    <div class="stat-card"><p>Pending Topups</p><h3>{{ $stats['pending_topups'] }}</h3></div>
    <div class="stat-card"><p>Users</p><h3>{{ $stats['users'] }}</h3></div>
</div>

<section class="panel mt-6">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">Latest Products</h2>
        <a href="{{ route('admin.products.index') }}" class="btn-ghost">View All</a>
    </div>

    <div class="table-wrap mt-4">
        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Subcategory</th>
                <th>Status</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            @forelse($latestProducts as $product)
                <tr>
                    <td>#{{ $product->id }}</td>
                    <td>{{ $product->name_en }}</td>
                    <td>{{ $product->subcategory?->name_en ?? '-' }}</td>
                    <td>
                        <span class="status-pill {{ $product->is_active ? 'ok' : 'off' }}">
                            {{ $product->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                    <td>${{ number_format((float) $product->selling_price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
