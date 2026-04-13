@extends('admin.layouts.app')
@section('title', 'Category Details')
@section('header', 'Category Details')
@section('content')
<section class="panel prose prose-slate max-w-none">
    <h2>{{ $category->name_en }}</h2>
    <p><strong>Arabic:</strong> {{ $category->name_ar }}</p>
    <p><strong>Slug:</strong> {{ $category->slug }}</p>
    <p><strong>Status:</strong> {{ $category->is_active ? 'Active' : 'Disabled' }}</p>
    <p><strong>SEO Title:</strong> {{ $category->seo_title ?: '-' }}</p>
    <p><strong>SEO Description:</strong> {{ $category->seo_description ?: '-' }}</p>
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
