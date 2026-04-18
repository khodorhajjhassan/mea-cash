@extends('admin.layouts.app')
 
@section('title', 'Add New Banner')
@section('header', 'Add New Banner')
 
@section('content')
<div class="max-w-4xl mx-auto">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">Banner Details</h2>
        </div>
 
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            @include('admin.banners._form')
 
            <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.banners.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Create Banner</button>
            </div>
        </form>
    </section>
</div>
@endsection
