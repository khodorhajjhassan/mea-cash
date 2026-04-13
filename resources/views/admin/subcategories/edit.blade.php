@extends('admin.layouts.app')
@section('title', 'Edit Subcategory')
@section('header', 'Edit Subcategory')
@section('content')
<section class="panel">
<form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.subcategories._form')<div class="mt-6 flex gap-2"><button type="submit" class="btn-primary">Save Changes</button><a href="{{ route('admin.subcategories.index') }}" class="btn-ghost">Cancel</a></div></form>
</section>
@endsection
