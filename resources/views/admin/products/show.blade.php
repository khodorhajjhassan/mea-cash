@extends('admin.layouts.app')
@section('title', 'Product Details')
@section('header', 'Product Details')
@section('content')
<section class="panel prose prose-slate max-w-none">
<h2>{{ $product->name_en }}</h2>
<p><strong>Arabic:</strong> {{ $product->name_ar }}</p>
<p><strong>Type:</strong> {{ str_replace('_', ' ', $product->product_type) }}</p>
<p><strong>Delivery:</strong> {{ $product->delivery_type }}</p>
<p><strong>Selling Price:</strong> ${{ number_format((float) $product->selling_price, 2) }}</p>
<p><strong>Subcategory:</strong> {{ $product->subcategory?->name_en ?? '-' }}</p>
<p><strong>Supplier:</strong> {{ $product->supplier?->name ?? '-' }}</p>
<a href="{{ route('admin.products.edit', $product) }}" class="btn-primary no-underline">Edit</a>
</section>

<section class="panel mt-6 grid gap-4 md:grid-cols-2">
    <form method="POST" action="{{ route('admin.products.packages.store', $product) }}" class="space-y-2">
        @csrf
        <h3 class="text-base font-semibold text-slate-900">Add Package</h3>
        <div class="field"><label>Name EN</label><input name="name_en" required></div>
        <div class="field"><label>Name AR</label><input name="name_ar" required></div>
        <div class="field"><label>Amount</label><input type="number" step="0.0001" name="amount" required></div>
        <div class="field"><label>Selling Price</label><input type="number" step="0.0001" name="selling_price"></div>
        <button class="btn-primary">Save Package</button>
    </form>

    <form method="POST" action="{{ route('admin.products.fields.store', $product) }}" class="space-y-2">
        @csrf
        <h3 class="text-base font-semibold text-slate-900">Add Dynamic Field</h3>
        <div class="field"><label>Field Key</label><input name="field_key" required></div>
        <div class="field"><label>Label EN</label><input name="label_en" required></div>
        <div class="field"><label>Label AR</label><input name="label_ar" required></div>
        <div class="field"><label>Type</label><select name="field_type"><option>text</option><option>email</option><option>password</option><option>number</option><option>select</option></select></div>
        <button class="btn-primary">Save Field</button>
    </form>
</section>
@endsection
