@extends('admin.layouts.app')
@section('title', 'Edit Category')
@section('header', 'Edit Category')
@section('content')
<section class="panel">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost">Cancel</a>
        </div>
    </form>
</section>
@endsection
