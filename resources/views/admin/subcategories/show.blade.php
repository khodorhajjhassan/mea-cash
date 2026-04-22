@extends('admin.layouts.app')
@section('title', 'Subcategory Details')
@section('header', 'Subcategory Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $subcategory->name_en }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-1">
            @if($subcategory->image)
                <x-admin.image :path="$subcategory->image" :alt="$subcategory->name_en" class="h-56 w-full rounded-xl object-cover" />
            @else
                <div class="flex h-56 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
            @endif
        </div>
        <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
            <div><p class="text-xs text-slate-500">Name EN</p><p class="font-medium text-slate-900">{{ $subcategory->name_en }}</p></div>
            <div><p class="text-xs text-slate-500">Name AR</p><p class="font-medium text-slate-900">{{ $subcategory->name_ar }}</p></div>
            <div><p class="text-xs text-slate-500">Category</p><p class="font-medium text-slate-900">{{ $subcategory->category?->name_en ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Template</p><p class="font-medium text-slate-900">{{ $subcategory->productTypeDefinition?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Slug</p><p class="font-medium text-slate-900">{{ $subcategory->slug }}</p></div>
            <div><p class="text-xs text-slate-500">Featured</p><p class="font-medium text-slate-900">{{ $subcategory->is_featured ? 'Yes' : 'No' }}</p></div>
        </div>
    </div>
</section>
@endsection
