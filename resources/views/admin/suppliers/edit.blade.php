@extends('admin.layouts.app')
@section('title','Edit Supplier')
@section('header','Edit Supplier')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">@csrf @method('PUT') @include('admin.suppliers._form')<div class="mt-6 flex gap-2"><button class="btn-primary">Save</button><a class="btn-ghost" href="{{ route('admin.suppliers.index') }}">Cancel</a></div></form></section>
@endsection
