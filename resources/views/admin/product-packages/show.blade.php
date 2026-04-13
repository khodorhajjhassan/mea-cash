@extends('admin.layouts.app')
@section('title', 'Package Details')
@section('header', 'Package Details')
@section('content')
<section class="panel prose prose-slate max-w-none">
<h2>{{ $productPackage->name_en }}</h2>
<p><strong>Arabic:</strong> {{ $productPackage->name_ar }}</p>
<p><strong>Product:</strong> {{ $productPackage->product?->name_en ?? '-' }}</p>
<p><strong>Amount:</strong> {{ $productPackage->amount }}</p>
<p><strong>Selling Price:</strong> ${{ number_format((float) $productPackage->selling_price, 2) }}</p>
<a href="{{ route('admin.product-packages.edit', $productPackage) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
