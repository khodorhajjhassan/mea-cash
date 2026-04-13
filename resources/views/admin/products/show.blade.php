@extends('admin.layouts.app')
@section('title', 'Product Details')
@section('header', 'Product Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
@php($typeLabel = match($product->product_type){'custom_quantity'=>'Top Up','account_topup'=>'Account',default=>'Key'})

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $product->name_en }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn-primary">Edit</a>
            <form method="POST" action="{{ route('admin.products.duplicate', $product) }}">
                @csrf
                <button type="submit" class="btn-ghost">Duplicate</button>
            </form>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-1">
            @if($product->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($product->image) }}" alt="{{ $product->name_en }}" class="h-56 w-full rounded-xl object-cover">
            @else
                <div class="flex h-56 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
            @endif
        </div>
        <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
            <div><p class="text-xs text-slate-500">Name EN</p><p class="font-medium text-slate-900">{{ $product->name_en }}</p></div>
            <div><p class="text-xs text-slate-500">Name AR</p><p class="font-medium text-slate-900">{{ $product->name_ar }}</p></div>
            <div><p class="text-xs text-slate-500">Slug</p><p class="font-medium text-slate-900">{{ $product->slug }}</p></div>
            <div><p class="text-xs text-slate-500">Type</p><p class="font-medium text-slate-900">{{ $typeLabel }}</p></div>
            <div><p class="text-xs text-slate-500">Template</p><p class="font-medium text-slate-900">{{ $product->subcategory?->productTypeDefinition?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Delivery</p><p class="font-medium text-slate-900">{{ ucfirst($product->delivery_type) }}</p></div>
            <div><p class="text-xs text-slate-500">Subcategory</p><p class="font-medium text-slate-900">{{ $product->subcategory?->name_en ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Supplier</p><p class="font-medium text-slate-900">{{ $product->supplier?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Status</p><p class="font-medium text-slate-900">{{ $product->is_active ? 'Active' : 'Disabled' }}</p></div>
            <div><p class="text-xs text-slate-500">Featured</p><p class="font-medium text-slate-900">{{ $product->is_featured ? 'Yes' : 'No' }}</p></div>
            <div><p class="text-xs text-slate-500">Selling Price</p><p class="font-medium text-slate-900">${{ number_format((float) $product->selling_price, 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Cost Price</p><p class="font-medium text-slate-900">${{ number_format((float) $product->cost_price, 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Price Per Unit</p><p class="font-medium text-slate-900">{{ $product->price_per_unit !== null ? '$'.number_format((float)$product->price_per_unit, 4) : '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Min / Max Quantity</p><p class="font-medium text-slate-900">{{ $product->min_quantity }} / {{ $product->max_quantity ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Stock Alert Threshold</p><p class="font-medium text-slate-900">{{ $product->stock_alert_threshold }}</p></div>
            <div><p class="text-xs text-slate-500">Sort Order</p><p class="font-medium text-slate-900">{{ $product->sort_order }}</p></div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
            <p class="text-xs text-slate-500">Description EN</p>
            <p class="mt-1 text-sm text-slate-800">{{ $product->description_en ?: '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Description AR</p>
            <p class="mt-1 text-sm text-slate-800">{{ $product->description_ar ?: '-' }}</p>
        </div>
    </div>

</section>

<section class="panel mt-6">
    <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Dynamic Fields</h3></div>
    <div class="table-wrap mt-3">
        <table class="admin-table">
            <thead><tr><th>Key</th><th>Label EN</th><th>Type</th><th>Required</th></tr></thead>
            <tbody>
            @forelse($product->formFields()->orderBy('sort_order')->get() as $field)
                <tr>
                    <td>{{ $field->field_key }}</td>
                    <td>{{ $field->label_en }}</td>
                    <td>{{ $field->field_type }}</td>
                    <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No dynamic fields.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
