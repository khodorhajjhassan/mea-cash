@extends('admin.layouts.app')
@section('title', 'Subcategory Details')
@section('header', 'Subcategory Details')
@section('content')
<section class="panel prose prose-slate max-w-none">
    <h2>{{ $subcategory->name_en }}</h2>
    <p><strong>Arabic:</strong> {{ $subcategory->name_ar }}</p>
    <p><strong>Category:</strong> {{ $subcategory->category?->name_en ?? '-' }}</p>
    <p><strong>Slug:</strong> {{ $subcategory->slug }}</p>
    <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
