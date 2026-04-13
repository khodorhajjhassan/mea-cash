@extends('admin.layouts.app')
@section('title', 'Create Package')
@section('header', 'Create Package')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.product-packages.store') }}" enctype="multipart/form-data">@csrf @include('admin.product-packages._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Create</button><a href="{{ route('admin.product-packages.index') }}" class="btn-ghost">Cancel</a></div></form></section>
@endsection
