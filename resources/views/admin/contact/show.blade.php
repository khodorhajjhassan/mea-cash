@extends('admin.layouts.app')
@section('title','Contact Message')
@section('header','Contact Message')
@section('content')
<section class="panel prose prose-slate max-w-none"><h2>{{ $contact->subject ?: 'No subject' }}</h2><p><strong>From:</strong> {{ $contact->name }} ({{ $contact->email }})</p><p>{{ $contact->message }}</p></section>
@endsection
