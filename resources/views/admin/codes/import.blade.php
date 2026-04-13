@extends('admin.layouts.app')
@section('title','Import Codes')
@section('header','Import Codes')
@section('content')
<section class="panel">
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.codes.import.store') }}" class="space-y-4">@csrf
  <div class="field"><label>Product</label><select name="product_id" required>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name_en }}</option>@endforeach</select></div>
  <div class="field"><label>CSV File</label><input type="file" name="csv_file" accept=".csv,.txt" required></div>
  <button type="submit" class="btn-primary">Import</button>
</form>
</section>
@endsection
