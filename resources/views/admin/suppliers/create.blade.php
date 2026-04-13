@extends('admin.layouts.app')
@section('title','Create Supplier')
@section('header','Create Supplier')
@section('content')
<section class="panel"><form method="POST" action="{{ route('admin.suppliers.store') }}">@csrf @include('admin.suppliers._form')<div class="mt-6 flex gap-2"><button class="btn-primary">Create</button><a class="btn-ghost" href="{{ route('admin.suppliers.index') }}">Cancel</a></div></form></section>
@endsection
