@extends('admin.layouts.app')
@section('title', 'Subcategory Details')
@section('header', 'Subcategory Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))
<section class="panel prose prose-slate max-w-none">
    <h2>{{ $subcategory->name_en }}</h2>
    @if($subcategory->image)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($subcategory->image) }}" alt="{{ $subcategory->name_en }}" class="my-4 h-48 w-48 rounded-xl object-cover not-prose">
    @endif
    <p><strong>Arabic:</strong> {{ $subcategory->name_ar }}</p>
    <p><strong>Category:</strong> {{ $subcategory->category?->name_en ?? '-' }}</p>
    <p><strong>Slug:</strong> {{ $subcategory->slug }}</p>
    <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn-primary no-underline">Edit</a>
</section>
@endsection
