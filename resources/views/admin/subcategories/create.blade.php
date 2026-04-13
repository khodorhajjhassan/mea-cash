@extends('admin.layouts.app')
@section('title', 'Create Subcategory')
@section('header', 'Create Subcategory')
@section('content')
<section class="panel">
<form method="POST" action="{{ route('admin.subcategories.store') }}" enctype="multipart/form-data">@csrf @include('admin.subcategories._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Create</button><a href="{{ route('admin.subcategories.index') }}" class="btn-ghost">Cancel</a></div></form>
</section>
@endsection
