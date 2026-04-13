@extends('admin.layouts.app')
@section('title','User Profile')
@section('header','User Profile')
@section('content')
<section class="panel grid gap-6 md:grid-cols-2">
<form method="POST" action="{{ route('admin.users.update',$user) }}" class="space-y-3">@csrf @method('PUT')
<div class="field"><label>Name</label><input name="name" value="{{ old('name',$user->name) }}" required></div>
<div class="field"><label>Email</label><input name="email" value="{{ old('email',$user->email) }}" required></div>
<div class="field"><label>Phone</label><input name="phone" value="{{ old('phone',$user->phone) }}"></div>
<div class="field"><label>Language</label><select name="preferred_language"><option value="en" @selected($user->preferred_language==='en')>EN</option><option value="ar" @selected($user->preferred_language==='ar')>AR</option></select></div>
<button class="btn-primary">Update User</button>
</form>
<form method="POST" action="{{ route('admin.users.credit',$user) }}" class="space-y-3">@csrf
<div class="field"><label>Credit Amount</label><input type="number" step="0.01" min="0.01" name="amount" required></div>
<div class="field"><label>Description</label><input name="description" required></div>
<button class="btn-ghost">Credit Wallet</button>
</form>
</section>
@endsection
