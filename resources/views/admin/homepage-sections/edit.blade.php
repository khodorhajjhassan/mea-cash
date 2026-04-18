@extends('admin.layouts.app')

@section('title', 'Edit Homepage Section')
@section('header', 'Edit Homepage Section')

@section('content')
<div class="mx-auto max-w-5xl">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">Edit {{ $homepageSection->title_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.homepage-sections.update', $homepageSection) }}" class="mt-6">
            @csrf
            @method('PUT')
            @include('admin.homepage-sections._form', ['section' => $homepageSection])

            <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.homepage-sections.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Update Section</button>
            </div>
        </form>
    </section>
</div>
@endsection
