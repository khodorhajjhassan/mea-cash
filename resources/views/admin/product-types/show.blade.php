@extends('admin.layouts.app')
@section('title', 'Product Type Details')
@section('header', 'Product Type Details')
@section('content')

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $productType->name }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div><p class="text-xs text-slate-500">Name</p><p class="font-medium text-slate-900">{{ $productType->name }}</p></div>
        <div><p class="text-xs text-slate-500">Key</p><p class="font-medium text-slate-900">{{ $productType->key }}</p></div>
        <div><p class="text-xs text-slate-500">Status</p><p class="font-medium text-slate-900">{{ $productType->is_active ? 'Active':'Inactive' }}</p></div>
        <div class="md:col-span-2">
            <p class="text-xs text-slate-500">Description</p>
            <p class="mt-1 text-sm text-slate-800">{{ $productType->description ?: '-' }}</p>
        </div>
    </div>
</section>

<section class="panel mt-6">
    <div class="panel-head"><h3 class="text-base font-semibold text-slate-900">Schema JSON</h3></div>
    <div class="mt-3">
        <pre class="rounded-xl bg-slate-950 p-4 text-xs text-slate-100 overflow-auto">{{ json_encode($productType->schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</section>
@endsection

