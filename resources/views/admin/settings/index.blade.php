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

<form method="GET" action="{{ route('admin.settings.index') }}" class="grid gap-3 md:grid-cols-4">
  <div class="field md:col-span-2"><label>Search</label><input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Group, key, value"></div>
  <div class="field"><label>Group</label><select name="group"><option value="">All</option>@foreach(['general','payment','seo'] as $group)<option value="{{ $group }}" @selected(($filters['group'] ?? '')===$group)>{{ ucfirst($group) }}</option>@endforeach</select></div>
  <div class="flex items-end gap-2"><button class="btn-primary" type="submit">Filter</button><a class="btn-ghost" href="{{ route('admin.settings.index') }}">Reset</a></div>
</form>

<div class="table-wrap"><table class="admin-table"><thead><tr><th>Group</th><th>Key</th><th>Value</th></tr></thead><tbody>@forelse($settings as $setting)<tr><td>{{ $setting->group }}</td><td>{{ $setting->key }}</td><td>{{ $setting->value }}</td></tr>@empty<tr><td colspan="3">No settings found.</td></tr>@endforelse</tbody></table></div>
<div class="mt-4">{{ $settings->links() }}</div>
<div class="flex gap-2"><a href="{{ route('admin.settings.payment') }}" class="btn-ghost">Payment Settings</a><a href="{{ route('admin.settings.seo') }}" class="btn-ghost">SEO Settings</a></div>
</section>
@endsection
