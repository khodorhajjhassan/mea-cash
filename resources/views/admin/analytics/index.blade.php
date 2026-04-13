@extends('admin.layouts.app')
@section('title','Analytics')
@section('header','Analytics')
@section('content')
<section class="grid gap-4 md:grid-cols-2"><div class="stat-card"><p>Total Revenue</p><h3>${{ number_format((float)$totalRevenue,2) }}</h3></div><div class="stat-card"><p>Total Orders</p><h3>{{ $totalOrders }}</h3></div></section>
<section class="panel mt-6"><p class="text-sm text-slate-600">JSON endpoints:</p><ul class="mt-2 list-disc pl-5 text-sm text-slate-700"><li><a class="underline" href="{{ route('admin.analytics.revenue') }}">Revenue</a></li><li><a class="underline" href="{{ route('admin.analytics.products') }}">Products</a></li><li><a class="underline" href="{{ route('admin.analytics.profit') }}">Profit</a></li></ul></section>
@endsection
