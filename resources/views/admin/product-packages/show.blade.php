@extends('admin.layouts.app')
@section('title', 'Package Details')
@section('header', 'Package Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel prose prose-slate max-w-none">
<h2>{{ $productPackage->name_en }}</h2>
@if($productPackage->image)
<img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($productPackage->image) }}" alt="{{ $productPackage->name_en }}" class="my-4 h-48 w-48 rounded-xl object-cover not-prose">
@endif
<p><strong>Arabic:</strong> {{ $productPackage->name_ar }}</p>
<p><strong>Product:</strong> {{ $productPackage->product?->name_en ?? '-' }}</p>
<p><strong>Amount:</strong> {{ $productPackage->amount }}</p>
<p><strong>Selling Price:</strong> ${{ number_format((float) $productPackage->selling_price, 2) }}</p>
<a href="{{ route('admin.product-packages.edit', $productPackage) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
