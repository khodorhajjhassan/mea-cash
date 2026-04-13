@extends('admin.layouts.app')
@section('title','Settings')
@section('header','System Settings')
@section('content')
<section class="panel space-y-4">
<form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-3 md:grid-cols-3">@csrf
  <input name="group" class="field" placeholder="group (general/payment/seo)" required>
  <input name="key" class="field" placeholder="key" required>
  <input name="value" class="field" placeholder="value">
  <button class="btn-primary md:col-span-3">Save Setting</button>
</form>
<div class="table-wrap"><table class="admin-table"><thead><tr><th>Group</th><th>Key</th><th>Value</th></tr></thead><tbody>@forelse($settings as $setting)<tr><td>{{ $setting->group }}</td><td>{{ $setting->key }}</td><td>{{ $setting->value }}</td></tr>@empty<tr><td colspan="3">No settings found.</td></tr>@endforelse</tbody></table></div>
<div class="flex gap-2"><a href="{{ route('admin.settings.payment') }}" class="btn-ghost">Payment Settings</a><a href="{{ route('admin.settings.seo') }}" class="btn-ghost">SEO Settings</a></div>
</section>
@endsection
