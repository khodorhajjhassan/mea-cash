@extends('admin.layouts.app')
@section('title','Topup Details')
@section('header','Top-up Details')
@section('content')
<section class="panel space-y-4">
<p><strong>User:</strong> {{ $topup->user?->name }}</p>
<p><strong>Method:</strong> {{ $topup->payment_method }}</p>
<p><strong>Amount:</strong> ${{ number_format((float)$topup->amount_requested,2) }}</p>
<p><strong>Status:</strong> {{ $topup->status }}</p>
<div class="grid gap-3 md:grid-cols-2">
<form method="POST" action="{{ route('admin.topups.approve',$topup) }}" class="space-y-2">@csrf<div class="field"><label>Approval Note</label><textarea name="admin_note"></textarea></div><button class="btn-primary">Approve</button></form>
<form method="POST" action="{{ route('admin.topups.reject',$topup) }}" class="space-y-2">@csrf<div class="field"><label>Reject Note</label><textarea name="admin_note" required></textarea></div><button class="btn-danger">Reject</button></form>
</div>
</section>
@endsection
