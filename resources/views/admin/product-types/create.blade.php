@extends('admin.layouts.app')
@section('title', 'Create Product Type')
@section('header', 'Create Product Type')
@section('content')
<section class="panel">
    <form method="POST" action="{{ route('admin.product-types.store') }}">@csrf @include('admin.product-types._form')
        <div class="mt-6 flex gap-2"><button class="btn-primary">Create</button><a href="{{ route('admin.product-types.index') }}" class="btn-ghost">Cancel</a></div>
    </form>
</section>
@endsection
