@extends('admin.layouts.app')
@section('title', __('admin.settings.title'))
@section('header', __('admin.settings.config'))
@section('content')

<div class="grid gap-6">
    <!-- Contact Information -->
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">{{ __('admin.settings.contact_info') }}</h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_name">
                <div class="field">
                    <label>{{ __('admin.settings.site_name') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" value="{{ $settings['site_name'] ?? '' }}" placeholder="MeaCash">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_email">
                <div class="field">
                    <label>{{ __('admin.settings.support_email') }}</label>
                    <div class="flex gap-2">
                        <input type="email" name="value" value="{{ $settings['site_email'] ?? '' }}" placeholder="support@meacash.com">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="general">
                <input type="hidden" name="key" value="site_phone">
                <div class="field">
                    <label>{{ __('admin.settings.support_phone') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" value="{{ $settings['site_phone'] ?? '' }}" placeholder="+961 ...">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="support">
                <input type="hidden" name="key" value="support_report_delay_hours">
                <div class="field">
                    <label>Support Report Delay (Hours)</label>
                    <div class="flex gap-2">
                        <input type="number" name="value" min="0" max="720" step="1" value="{{ $settings['support_report_delay_hours'] ?? 4 }}" placeholder="4">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Customers can open an order support report only after this many hours from fulfillment. Use 0 to allow immediately.</p>
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
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">{{ __('admin.settings.social_links') }}</h2>
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
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </section>

    <!-- SEO & Metadata -->
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 uppercase tracking-wider">{{ __('admin.settings.seo_group') }}</h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-1">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="seo">
                <input type="hidden" name="key" value="meta_title">
                <div class="field">
                    <label>{{ __('admin.settings.meta_title') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" class="grow" value="{{ $seo['meta_title'] ?? '' }}" placeholder="MeaCash - Best Digitial Marketplace">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="seo">
                <input type="hidden" name="key" value="meta_description">
                <div class="field">
                    <label>{{ __('admin.settings.meta_desc') }}</label>
                    <div class="flex gap-2">
                        <textarea name="value" class="grow" rows="2">{{ $seo['meta_description'] ?? '' }}</textarea>
                        <button class="btn-primary py-2 px-6 h-fit mt-auto">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group" value="seo">
                <input type="hidden" name="key" value="meta_keywords">
                <div class="field">
                    <label>{{ __('admin.settings.meta_keys') }}</label>
                    <div class="flex gap-2">
                        <input type="text" name="value" class="grow" value="{{ $seo['meta_keywords'] ?? '' }}" placeholder="gaming, cards, giftcards, topup">
                        <button class="btn-primary py-2 px-6">{{ __('admin.common.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@endsection
