@extends('admin.layouts.app')
@section('title', 'Create Category')
@section('header', 'Create Category')
@section('content')
<section class="panel">
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.categories._form')
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">Create</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost">Cancel</a>
        </div>
    </form>
</section>
@endsection
