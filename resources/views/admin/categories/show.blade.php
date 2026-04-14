@extends('admin.layouts.app')
@section('title', 'Category Details')
@section('header', 'Category Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $category->name_en }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-1">
            @if($category->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($category->image) }}" alt="{{ $category->name_en }}" class="h-56 w-full rounded-xl object-cover">
            @else
                <div class="flex h-56 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
            @endif
        </div>
        <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
            <div><p class="text-xs text-slate-500">Name EN</p><p class="font-medium text-slate-900">{{ $category->name_en }}</p></div>
            <div><p class="text-xs text-slate-500">Name AR</p><p class="font-medium text-slate-900">{{ $category->name_ar }}</p></div>
            <div><p class="text-xs text-slate-500">Slug</p><p class="font-medium text-slate-900">{{ $category->slug }}</p></div>
            <div><p class="text-xs text-slate-500">Status</p><p class="font-medium text-slate-900">{{ $category->is_active ? 'Active' : 'Disabled' }}</p></div>
            <div><p class="text-xs text-slate-500">SEO Title</p><p class="font-medium text-slate-900">{{ $category->seo_title ?: '-' }}</p></div>
            <div><p class="text-xs text-slate-500">SEO Description</p><p class="font-medium text-slate-900">{{ $category->seo_description ?: '-' }}</p></div>
        </div>
    </div>
</section>
@endsection

