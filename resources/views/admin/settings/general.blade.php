@extends('admin.layouts.app')
@section('title', 'General Settings')
@section('header', 'System Configuration')
@section('content')

<div class="grid gap-6">
    <!-- Contact Information -->
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">Contact Information</h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_name">
                <div class="field">
                    <label>Site Name</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" value="{{ $settings['site_name'] ?? '' }}" placeholder="MeaCash">
                        <button class="btn-primary py-2 px-6">Save</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_email">
                <div class="field">
                    <label>Support Email</label>
                    <div class="flex gap-2">
                        <input type="email" name="value" value="{{ $settings['site_email'] ?? '' }}" placeholder="support@meacash.com">
                        <button class="btn-primary py-2 px-6">Save</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_phone">
                <div class="field">
                    <label>Support Phone</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" value="{{ $settings['site_phone'] ?? '' }}" placeholder="+961 ...">
                        <button class="btn-primary py-2 px-6">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Social Media -->
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.103 1.103"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">Social Media Links</h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach(['facebook', 'instagram', 'twitter', 'whatsapp'] as $platform)
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="social">
                <input type="hidden" name="key" value="social_{{ $platform }}">
                <div class="field">
                    <label class="capitalize">{{ $platform }} URL</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" value="{{ $social['social_'.$platform] ?? '' }}" placeholder="https://{{ $platform }}.com/...">
                        <button class="btn-primary py-2 px-6">Save</button>
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </section>
</div>

@endsection
