@extends('admin.layouts.app')
@section('title','User Transactions')
@section('header','User Transaction History')
@section('content')
<section class="panel"><p class="mb-4 text-sm text-slate-600">User: {{ $user->name }} (#{{ $user->id }})</p><div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>Type</th><th>Amount</th><th>When</th></tr></thead><tbody>@forelse($transactions as $tx)<tr><td>#{{ $tx->id }}</td><td>{{ $tx->type }}</td><td>{{ $tx->amount }}</td><td>{{ $tx->created_at }}</td></tr>@empty<tr><td colspan="4">No transactions found.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $transactions->links() }}</div></section>
@endsection
