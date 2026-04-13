@extends('admin.layouts.app')
@section('title', 'Edit Package')
@section('header', 'Edit Package')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.product-packages.update', $productPackage) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.product-packages._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Save Changes</button><a href="{{ route('admin.product-packages.index') }}" class="btn-ghost">Cancel</a></div></form></section>
@endsection
