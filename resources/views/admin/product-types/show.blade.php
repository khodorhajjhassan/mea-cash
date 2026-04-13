@extends('admin.layouts.app')
@section('title', 'Product Type Details')
@section('header', 'Product Type Details')
@section('content')
<section class="panel prose prose-slate max-w-none">
    <h2>{{ $productType->name }}</h2>
    <p><strong>Key:</strong> {{ $productType->key }}</p>
    <p><strong>Status:</strong> {{ $productType->is_active ? 'Active':'Inactive' }}</p>
    <p><strong>Description:</strong> {{ $productType->description ?: '-' }}</p>
    <h3>Schema JSON</h3>
    <pre class="rounded-xl bg-slate-950 p-4 text-xs text-slate-100 overflow-auto">{{ json_encode($productType->schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
