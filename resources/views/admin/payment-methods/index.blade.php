@extends('admin.layouts.app')
@section('title','Payment Methods')
@section('header','Payment Methods')
@section('content')
<section class="panel space-y-4">
<form method="GET" action="{{ route('admin.payment-methods.index') }}" class="grid gap-3 md:grid-cols-4">
<div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Method, label, account"></div>
<div class="field"><label>Status</label><select name="status"><option value="">All</option><option value="active" @selected(($filters['status'] ?? '')==='active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '')==='inactive')>Inactive</option></select></div>
<div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.payment-methods.index') }}">Reset</a></div>
</form>
@foreach($methods as $method)
<form method="POST" action="{{ route('admin.payment-methods.update', $method) }}" class="rounded-xl border border-slate-200 p-4">@csrf @method('PUT')
<div class="grid gap-3 md:grid-cols-2">
<div class="field"><label>Method</label><input value="{{ $method->method }}" disabled></div>
<div class="field"><label>Account Identifier</label><input name="account_identifier" value="{{ old('account_identifier',$method->account_identifier) }}" required></div>
<div class="field"><label>Display EN</label><input name="display_name_en" value="{{ old('display_name_en',$method->display_name_en) }}" required></div>
<div class="field"><label>Display AR</label><input name="display_name_ar" value="{{ old('display_name_ar',$method->display_name_ar) }}" required></div>
<div class="field"><label>Instructions EN</label><textarea name="instructions_en">{{ old('instructions_en',$method->instructions_en) }}</textarea></div>
<div class="field"><label>Instructions AR</label><textarea name="instructions_ar">{{ old('instructions_ar',$method->instructions_ar) }}</textarea></div>
</div>
<div class="mt-3 flex gap-2"><button class="btn-primary">Save</button></form><form method="POST" action="{{ route('admin.payment-methods.toggle',$method) }}">@csrf<button class="btn-danger-outline">{{ $method->is_active ? 'Disable' : 'Enable' }}</button></form></div>
@endforeach
</section>
@endsection
