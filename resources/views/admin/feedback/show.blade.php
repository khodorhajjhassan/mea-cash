@extends('admin.layouts.app')
@section('title','Feedback Details')
@section('header','Feedback Details')
@section('content')
<section class="panel prose prose-slate max-w-none"><p><strong>User:</strong> {{ $feedback->user?->name ?? '-' }}</p><p><strong>Order:</strong> {{ $feedback->order?->order_number ?? '-' }}</p><p><strong>Rating:</strong> {{ $feedback->rating }}/5</p><p><strong>Comment:</strong> {{ $feedback->comment ?: '-' }}</p></section>
@endsection
