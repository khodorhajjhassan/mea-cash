@extends('admin.layouts.app')
@section('title', 'Edit Product Type')
@section('header', 'Edit Product Type')
@section('content')
<section class="panel">
    <form method="POST" action="{{ route('admin.product-types.update', $productType) }}">@csrf @method('PUT') @include('admin.product-types._form')
        <div class="mt-6 flex gap-2"><button class="btn-primary">Save Changes</button><a href="{{ route('admin.product-types.index') }}" class="btn-ghost">Cancel</a></div>
    </form>
</section>
@endsection
