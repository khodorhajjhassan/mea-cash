@extends('admin.layouts.app')
@section('title','Top-ups')
@section('header','Top-up Requests')
@section('content')
<section class="panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($topups as $topup)
<tr><td>#{{ $topup->id }}</td><td>{{ $topup->user?->name ?? '-' }}</td><td>${{ number_format((float)$topup->amount_requested,2) }}</td><td>{{ $topup->status }}</td><td class="flex gap-2"><a class="btn-ghost" href="{{ route('admin.topups.show',$topup) }}">View</a></td></tr>
@empty <tr><td colspan="5">No topup requests.</td></tr> @endforelse
</tbody></table></div><div class="mt-4">{{ $topups->links() }}</div></section>
@endsection
