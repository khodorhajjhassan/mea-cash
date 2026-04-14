@extends('admin.layouts.app')
@section('title', 'System Settings')
@section('header', 'System Hub')
@section('content')

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    <!-- General Settings Hub -->
    <a href="{{ route('admin.settings.general') }}" class="panel hover:border-indigo-200 transition-all group">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">General Configuration</h3>
                <p class="text-xs text-slate-500">Contact info, support details, and social presence.</p>
            </div>
        </div>
    </a>

    <!-- SEO Hub -->
    <a href="{{ route('admin.settings.seo') }}" class="panel hover:border-emerald-200 transition-all group">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">SEO Optimization</h3>
                <p class="text-xs text-slate-500">Manage Meta tags, descriptions, and keywords.</p>
            </div>
        </div>
    </a>

    <!-- Legal Pages Hub -->
    <a href="{{ route('admin.pages.edit') }}" class="panel hover:border-rose-200 transition-all group">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l5 5v11a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v5h5"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Legal & Info Pages</h3>
                <p class="text-xs text-slate-500">Dynamic content for About, Terms, and Privacy.</p>
            </div>
        </div>
    </a>

    <!-- Raw Settings (EAV) -->
    <section class="panel lg:col-span-3">
        <div class="panel-head border-b border-slate-100 pb-3 mb-4">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Master Settings Registry</h2>
        </div>
        
        <form method="GET" action="{{ route('admin.settings.index') }}" class="mb-4 grid gap-3 md:grid-cols-4">
            <div class="field md:col-span-2">
                <label>Search Data</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Key or Value...">
            </div>
            <div class="flex items-end gap-2">
                <button class="btn-primary" type="submit">Filter</button>
                <a class="btn-ghost" href="{{ route('admin.settings.index') }}">Reset</a>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                    <tr>
                        <td><span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold uppercase text-slate-600 tracking-tighter">{{ $setting->group }}</span></td>
                        <td class="font-mono text-xs text-slate-700">{{ $setting->key }}</td>
                        <td class="text-xs text-slate-500 truncate max-w-[200px]">{{ $setting->value }}</td>
                        <td>
                             <form method="POST" action="{{ route('admin.settings.update') }}" class="inline-flex gap-2">
                                @csrf
                                <input type="hidden" name="group" value="{{ $setting->group }}">
                                <input type="hidden" name="key" value="{{ $setting->key }}">
                                <input type="text" name="value" value="{{ $setting->value }}" class="text-xs py-1 px-2 border-slate-200">
                                <button class="btn-ghost text-[10px] py-1 px-2">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-slate-400">No settings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $settings->links() }}</div>
    </section>
</div>

@endsection
