@extends('admin.layouts.app')
@section('title', 'SEO Management')
@section('header', 'Search Engine Optimization')

@section('content')
<div x-data="seoPreview({
    title: '{{ $settings['seo_title_template'] ?? '' }}',
    separator: '{{ $settings['seo_title_separator'] ?? '—' }}',
    description: '{{ $settings['seo_default_description'] ?? '' }}',
    ogTitle: '{{ $settings['og_default_title'] ?? '' }}',
    ogDesc: '{{ $settings['og_default_description'] ?? '' }}'
})" class="space-y-6">

    <form method="POST" action="{{ route('admin.settings.seo.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Settings -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Category 1: Basic Meta -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">1. Basic Meta Tags</h2>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="field">
                            <label>Title Template</label>
                            <input type="text" name="settings[seo_title_template]" x-model="title" placeholder="{page_title} — MeaCash">
                            <p class="text-[10px] text-slate-400 mt-1">Use <code>{page_title}</code> as placeholder for dynamic titles.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="field">
                                <label>Title Separator</label>
                                <input type="text" name="settings[seo_title_separator]" x-model="separator" placeholder="—">
                            </div>
                            <div class="field">
                                <label>Robots Default</label>
                                <select name="settings[seo_robots_default]">
                                    <option value="index, follow" {{ ($settings['seo_robots_default'] ?? '') == 'index, follow' ? 'selected' : '' }}>Index, Follow</option>
                                    <option value="noindex, nofollow" {{ ($settings['seo_robots_default'] ?? '') == 'noindex, nofollow' ? 'selected' : '' }}>Noindex, Nofollow</option>
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Default Meta Description</label>
                            <textarea name="settings[seo_default_description]" x-model="description" rows="3"></textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-[10px] text-slate-400">Recommended: 150-160 characters.</p>
                                <p class="text-[10px] font-bold" :class="description.length > 160 ? 'text-rose-500' : 'text-emerald-500'" x-text="description.length + '/160'"></p>
                            </div>
                        </div>
                        <div class="field">
                            <label>Default Keywords</label>
                            <input type="text" name="settings[seo_default_keywords]" value="{{ $settings['seo_default_keywords'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Canonical Domain</label>
                            <input type="url" name="settings[seo_canonical_domain]" value="{{ $settings['seo_canonical_domain'] ?? '' }}" placeholder="https://meacash.com">
                        </div>
                    </div>
                </section>

                <!-- Category 2: Open Graph -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">2. Open Graph (FB/WA)</h2>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="field">
                            <label>Default OG Title</label>
                            <input type="text" name="settings[og_default_title]" x-model="ogTitle">
                        </div>
                        <div class="field">
                            <label>Default OG Description</label>
                            <textarea name="settings[og_default_description]" x-model="ogDesc" rows="2"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="field">
                                <label>Site Name</label>
                                <input type="text" name="settings[og_site_name]" value="{{ $settings['og_site_name'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label>Default Image (1200x630)</label>
                                <input type="file" name="files[og_default_image]" accept="image/*" class="text-xs">
                                @if($settings['og_default_image'] ?? null)
                                    <div class="mt-2 h-20 w-32 rounded border border-slate-200 overflow-hidden bg-slate-50">
                                        <x-admin.image :path="$settings['og_default_image']" class="h-full w-full object-cover" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Category 3: Twitter Cards -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.451-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">3. Twitter Cards</h2>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="field">
                                <label>Card Type</label>
                                <select name="settings[twitter_card_type]">
                                    <option value="summary" {{ ($settings['twitter_card_type'] ?? '') == 'summary' ? 'selected' : '' }}>Summary</option>
                                    <option value="summary_large_image" {{ ($settings['twitter_card_type'] ?? '') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Twitter Handle</label>
                                <input type="text" name="settings[twitter_site_handle]" value="{{ $settings['twitter_site_handle'] ?? '' }}" placeholder="@meacash">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Category 4: Google & Technical -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">4. Google & Tracking</h2>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="field">
                            <label>Google Analytics ID</label>
                            <input type="text" name="settings[google_analytics_id]" value="{{ $settings['google_analytics_id'] ?? '' }}" placeholder="G-XXXXXXXXXX">
                            <p class="text-[9px] text-slate-400 mt-1">Starts with G-</p>
                        </div>
                        <div class="field">
                            <label>Google Tag Manager ID</label>
                            <input type="text" name="settings[google_tag_manager_id]" value="{{ $settings['google_tag_manager_id'] ?? '' }}" placeholder="GTM-XXXXXXX">
                            <p class="text-[9px] text-slate-400 mt-1">Starts with GTM-</p>
                        </div>
                        <div class="field">
                            <label>Site Verification Tag</label>
                            <input type="text" name="settings[google_site_verification]" value="{{ $settings['google_site_verification'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Googlebot Directive</label>
                            <input type="text" name="settings[googlebot_directive]" value="{{ $settings['googlebot_directive'] ?? '' }}" placeholder="index, follow">
                        </div>
                    </div>
                </section>

                <!-- Category 5: Social Pixels -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-pink-100 flex items-center justify-center text-pink-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">5. Conversion Pixels</h2>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="field">
                            <label>Facebook Pixel ID</label>
                            <input type="text" name="settings[facebook_pixel_id]" value="{{ $settings['facebook_pixel_id'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>TikTok Pixel ID</label>
                            <input type="text" name="settings[tiktok_pixel_id]" value="{{ $settings['tiktok_pixel_id'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Snapchat Pixel ID</label>
                            <input type="text" name="settings[snapchat_pixel_id]" value="{{ $settings['snapchat_pixel_id'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>FB Domain Verification</label>
                            <input type="text" name="settings[facebook_domain_verification]" value="{{ $settings['facebook_domain_verification'] ?? '' }}">
                        </div>
                    </div>
                </section>

                <!-- Category 6: Schema & Technical -->
                <section class="panel">
                    <div class="panel-head border-b border-slate-100 pb-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">6. Structured Data (Schema)</h2>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="field">
                            <label>Business Name</label>
                            <input type="text" name="settings[schema_business_name]" value="{{ $settings['schema_business_name'] ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Business Type</label>
                            <input type="text" name="settings[schema_business_type]" value="{{ $settings['schema_business_type'] ?? '' }}" placeholder="OnlineStore">
                        </div>
                        <div class="field">
                            <label>Business Logo</label>
                            <input type="file" name="files[schema_logo_url]" accept="image/*" class="text-xs">
                            @if($settings['schema_logo_url'] ?? null)
                                <div class="mt-2 h-12 w-12 rounded border p-1 border-slate-200 bg-slate-50">
                                    <x-admin.image :path="$settings['schema_logo_url']" class="h-full w-full object-contain" />
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="settings[sitemap_enabled]" value="0">
                                <input type="checkbox" name="settings[sitemap_enabled]" value="1" {{ ($settings['sitemap_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">Enable Sitemap</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="settings[hreflang_enabled]" value="0">
                                <input type="checkbox" name="settings[hreflang_enabled]" value="1" {{ ($settings['hreflang_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">Enable Hreflang</span>
                            </label>
                             <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="settings[breadcrumb_schema_enabled]" value="0">
                                <input type="checkbox" name="settings[breadcrumb_schema_enabled]" value="1" {{ ($settings['breadcrumb_schema_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700">Enable Breadcrumb Schema</span>
                            </label>
                        </div>
                    </div>
                </section>

                <div class="sticky bottom-6 bg-white/80 backdrop-blur p-4 rounded-2xl border border-slate-200 shadow-xl flex justify-end z-[40]">
                    <button type="submit" class="btn-primary px-10 py-3 shadow-lg shadow-indigo-200 hover:scale-105 active:scale-95 transition-all">
                        Save SEO Configuration
                    </button>
                </div>
            </div>

            <!-- Right Column: Previews -->
            <div class="space-y-6">
                <div class="sticky top-24 space-y-6">
                    <!-- Google Preview -->
                    <div class="panel border-t-4 border-t-blue-500 overflow-hidden shadow-lg">
                        <div class="panel-head border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Google Search Snippet</h3>
                        </div>
                        <div class="bg-white p-4 rounded-xl">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-400 font-bold">MC</div>
                                <cite class="text-[14px] text-slate-600 not-italic">https://meacash.com <span class="text-slate-400">› sample-product</span></cite>
                            </div>
                            <h4 class="text-[20px] text-blue-700 hover:underline cursor-pointer mb-1 leading-tight" x-text="resolvedTitle"></h4>
                            <p class="text-[14px] text-slate-600 leading-normal line-clamp-2" x-text="description || 'Provide a meta description to see how it looks here in Google search results...'"></p>
                        </div>
                    </div>

                    <!-- Facebook Preview -->
                    <div class="panel border-t-4 border-t-indigo-600 overflow-hidden shadow-lg">
                        <div class="panel-head border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Facebook / WhatsApp Link</h3>
                        </div>
                        <div class="bg-white rounded-xl overflow-hidden border border-slate-200">
                            <div class="aspect-video bg-slate-100 flex items-center justify-center text-slate-300">
                                @if($settings['og_default_image'] ?? null)
                                    <x-admin.image :path="$settings['og_default_image']" class="w-full h-full object-cover" />
                                @else
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div class="p-3 bg-slate-50 border-t border-slate-100">
                                <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-0.5">MEACASH.COM</p>
                                <h4 class="text-[16px] font-bold text-slate-800 leading-tight mb-0.5" x-text="ogTitle || 'MeaCash Lebanon'"></h4>
                                <p class="text-[13px] text-slate-500 line-clamp-1" x-text="ogDesc || 'Your ultimate destination for gaming cards and instant delivery in Lebanon.'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seoPreview', (initial) => ({
        title: initial.title,
        separator: initial.separator,
        description: initial.description,
        ogTitle: initial.ogTitle,
        ogDesc: initial.ogDesc,
        
        get resolvedTitle() {
            let t = this.title || '{page_title} — MeaCash';
            return t.replace('{page_title}', 'Sample Product Name');
        }
    }));
});
</script>
@endpush
@endsection
