@extends('admin.layouts.app')
@section('title', 'Create Product')
@section('header', 'Create Product')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">@csrf @include('admin.products._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Create</button><a href="{{ route('admin.products.index') }}" class="btn-ghost">Cancel</a></div></form></section>
@endsection
