@extends('admin.layouts.app')
@section('title', 'Edit Product')
@section('header', 'Edit Product')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.products._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Save Changes</button><a href="{{ route('admin.products.index') }}" class="btn-ghost">Cancel</a></div></form></section>
@endsection
