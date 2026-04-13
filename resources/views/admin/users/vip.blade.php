@extends('admin.layouts.app')
@section('title','VIP Customers')
@section('header','VIP Customers')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>User</th><th>Completed Spend</th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>{{ $user->name }}</td><td>${{ number_format((float)$user->orders->where('status','completed')->sum('total_price'),2) }}</td></tr>
@empty <tr><td colspan="2">No data available.</td></tr> @endforelse
</tbody></table></div></section>
@endsection
