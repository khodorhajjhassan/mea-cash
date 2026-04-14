@extends('admin.layouts.app')
@section('title', 'Package Details')
@section('header', 'Package Details')
@section('content')
@php($disk = config('media.disk', config('filesystems.default')))

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $productPackage->name_en }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.product-packages.edit', $productPackage) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-1">
            @if($productPackage->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk($disk)->url($productPackage->image) }}" alt="{{ $productPackage->name_en }}" class="h-56 w-full rounded-xl object-cover">
            @else
                <div class="flex h-56 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
            @endif
        </div>
        <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
            <div><p class="text-xs text-slate-500">Name EN</p><p class="font-medium text-slate-900">{{ $productPackage->name_en }}</p></div>
            <div><p class="text-xs text-slate-500">Name AR</p><p class="font-medium text-slate-900">{{ $productPackage->name_ar }}</p></div>
            <div><p class="text-xs text-slate-500">Product</p><p class="font-medium text-slate-900">{{ $productPackage->product?->name_en ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-500">Amount</p><p class="font-medium text-slate-900">{{ $productPackage->amount }}</p></div>
            <div><p class="text-xs text-slate-500">Selling Price</p><p class="font-medium text-slate-900">${{ number_format((float) $productPackage->selling_price, 2) }}</p></div>
        </div>
    </div>
</section>
@endsection

