@extends('admin.layouts.app')
@section('title','Transactions')
@section('header','Wallet Transactions')
@section('content')
<section class="panel space-y-4">
<form method="POST" action="{{ route('admin.transactions.adjust') }}" class="grid gap-3 md:grid-cols-4">@csrf
  <input type="number" name="user_id" class="field" placeholder="User ID" required>
  <input type="number" step="0.01" name="amount" class="field" placeholder="+/- Amount" required>
  <input type="text" name="description" class="field" placeholder="Reason" required>
  <button class="btn-primary">Adjust Wallet</button>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Type</th><th>Amount</th><th>When</th></tr></thead><tbody>
@forelse($transactions as $tx)
<tr><td>#{{ $tx->id }}</td><td>{{ $tx->wallet?->user?->name ?? '-' }}</td><td>{{ $tx->type }}</td><td>{{ $tx->amount }}</td><td>{{ $tx->created_at }}</td></tr>
@empty <tr><td colspan="5">No transactions.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $transactions->links() }}</div>
</section>
@endsection
