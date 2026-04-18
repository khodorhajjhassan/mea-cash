@extends('admin.layouts.app')

@section('title', 'Add Homepage Section')
@section('header', 'Add Homepage Section')

@section('content')
<div class="mx-auto max-w-5xl">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">Section Details</h2>
        </div>

        <form method="POST" action="{{ route('admin.homepage-sections.store') }}" class="mt-6">
            @csrf
            @include('admin.homepage-sections._form')

            <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.homepage-sections.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Create Section</button>
            </div>
        </form>
    </section>
</div>
@endsection
