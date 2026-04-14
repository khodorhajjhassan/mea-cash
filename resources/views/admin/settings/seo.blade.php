@extends('admin.layouts.app')
@section('title', 'SEO Settings')
@section('header', 'SEO Optimization')
@section('content')

<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">Search Engine Optimization</h2>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-6">
                @foreach([
                    'seo_title' => 'Meta Title',
                    'seo_description' => 'Meta Description',
                    'seo_keywords' => 'Meta Keywords',
                    'seo_author' => 'Author Name'
                ] as $key => $label)
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="group" value="seo">
                    <input type="hidden" name="key" value="{{ $key }}">
                    <div class="field">
                        <label>{{ $label }}</label>
                        @if($key === 'seo_description')
                            <textarea name="value" rows="3" placeholder="Enter SEO description...">{{ $settings[$key] ?? '' }}</textarea>
                        @else
                            <input type="text" name="value" value="{{ $settings[$key] ?? '' }}" placeholder="Enter {{ strtolower($label) }}...">
                        @endif
                    </div>
                    <div class="flex justify-end">
                        <button class="btn-primary py-1.5 px-4 text-xs font-bold uppercase tracking-widest leading-none">Update</button>
                    </div>
                </form>
                @endforeach
            </div>

            <div class="space-y-6">
                 @foreach([
                    'seo_og_title' => 'OpenGraph Title',
                    'seo_og_description' => 'OpenGraph Description',
                    'seo_og_image' => 'OpenGraph Image URL',
                    'seo_twitter_handle' => 'Twitter Handle (e.g. @meacash)'
                ] as $key => $label)
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="group" value="seo">
                    <input type="hidden" name="key" value="{{ $key }}">
                    <div class="field">
                        <label>{{ $label }}</label>
                        <input type="text" name="value" value="{{ $settings[$key] ?? '' }}" placeholder="Enter {{ strtolower($label) }}...">
                    </div>
                    <div class="flex justify-end">
                        <button class="btn-primary py-1.5 px-4 text-xs font-bold uppercase tracking-widest leading-none">Update</button>
                    </div>
                </form>
                @endforeach

                <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100 border-dashed">
                    <h4 class="text-xs font-bold text-indigo-700 uppercase mb-2">SEO Preview</h4>
                    <div class="bg-white p-4 rounded-lg border border-slate-200">
                        <div class="text-blue-600 text-lg font-medium truncate mb-1">{{ $settings['seo_title'] ?? 'Site Title Here' }}</div>
                        <div class="text-green-700 text-xs truncate mb-2">{{ config('app.url') }} › ...</div>
                        <div class="text-slate-600 text-sm line-clamp-2">{{ $settings['seo_description'] ?? 'Your site meta description will appear here in search results.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
