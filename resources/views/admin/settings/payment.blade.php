@extends('admin.layouts.app')
@section('title','Payment Settings')
@section('header','Payment Settings')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>@forelse($settings as $setting)<tr><td>{{ $setting->key }}</td><td>{{ $setting->value }}</td></tr>@empty<tr><td colspan="2">No payment settings found.</td></tr>@endforelse</tbody></table></div></section>
@endsection
