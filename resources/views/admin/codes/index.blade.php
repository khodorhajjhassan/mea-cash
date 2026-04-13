@extends('admin.layouts.app')
@section('title','Code Inventory')
@section('header','Code Inventory')
@section('content')
<section class="panel">
  <div class="panel-head"><h2 class="text-lg font-semibold">All Codes</h2><a href="{{ route('admin.codes.import') }}" class="btn-primary">Import CSV</a></div>
  <form method="POST" action="{{ route('admin.codes.store') }}" class="mt-4 grid gap-3 md:grid-cols-4">@csrf
    <input class="field" type="number" name="product_id" placeholder="Product ID" required>
    <input class="field" type="number" name="package_id" placeholder="Package ID">
    <input class="field" type="text" name="code" placeholder="Code" required>
    <button class="btn-primary" type="submit">Add Code</button>
  </form>
  <div class="table-wrap mt-4"><table class="admin-table"><thead><tr><th>ID</th><th>Product</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody>
  @forelse($codes as $code)
    <tr><td>#{{ $code->id }}</td><td>{{ $code->product?->name_en ?? '-' }}</td><td>{{ $code->status }}</td><td>{{ $code->created_at }}</td><td><button type="button" class="btn-danger-outline js-delete-button" data-action="{{ route('admin.codes.destroy', $code) }}" data-name="Code #{{ $code->id }}">Delete</button></td></tr>
  @empty
    <tr><td colspan="5">No codes found.</td></tr>
  @endforelse
  </tbody></table></div>
  <div class="mt-4">{{ $codes->links() }}</div>
</section>
@endsection
