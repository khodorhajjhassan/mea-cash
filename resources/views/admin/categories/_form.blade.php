@php($editing = isset($category))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field">
        <label>Name (EN)</label>
        <input type="text" name="name_en" value="{{ old('name_en', $category->name_en ?? '') }}" required>
    </div>
    <div class="field">
        <label>Name (AR)</label>
        <input type="text" name="name_ar" value="{{ old('name_ar', $category->name_ar ?? '') }}" required>
    </div>
    <div class="field">
        <label>Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="auto from name">
    </div>
    <div class="field">
        <label>Icon</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}">
    </div>
    <div class="field">
        <label>Sort Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </div>
    <div class="field">
        <label>Status</label>
        <select name="is_active">
            <option value="1" @selected(old('is_active', $category->is_active ?? true) == 1)>Active</option>
            <option value="0" @selected(old('is_active', $category->is_active ?? true) == 0)>Disabled</option>
        </select>
    </div>
</div>

<div class="field mt-4">
    <label>Main Category Image</label>
    <input type="file" name="image" accept="image/*" class="text-xs">
    @if ($editing && $category->image)
        <div class="mt-2 h-20 w-32 rounded border border-slate-200 overflow-hidden shadow-sm bg-white">
            <img src="{{ asset('storage/' . $category->image) }}" class="h-full w-full object-cover">
        </div>
        <p class="hint">Uploading a new image will replace the current one.</p>
    @endif
</div>

<div class="mt-8 border-t border-slate-100 pt-8" x-data="{ open: false }">
    <button type="button" @click="open = !open" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest text-xs font-bold mb-4">
        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        Search Engine Optimization (SEO)
    </button>
    
    <div x-show="open" x-cloak class="grid gap-4 md:grid-cols-2 bg-slate-50/50 p-6 rounded-2xl border border-slate-100 mb-6">
        <div class="field">
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $category->seo_title ?? '') }}" maxlength="70">
            <p class="text-[9px] text-slate-400 mt-1">Recommended: 60 characters.</p>
        </div>
        <div class="field">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $category->seo_keywords ?? '') }}" placeholder="gaming, cards, category">
        </div>
        <div class="field md:col-span-2">
            <label>SEO Description</label>
            <textarea name="seo_description" rows="2" maxlength="160">{{ old('seo_description', $category->seo_description ?? '') }}</textarea>
            <p class="text-[9px] text-slate-400 mt-1">Recommended: 150-160 characters.</p>
        </div>
        <div class="field">
            <label>SEO Social Image</label>
            <input type="file" name="seo_image" accept="image/*" class="text-xs">
            @if($editing && $category->seo_image)
                <div class="mt-2 h-20 w-32 rounded border border-slate-200 overflow-hidden shadow-sm bg-white">
                    <img src="{{ asset('storage/' . $category->seo_image) }}" class="h-full w-full object-cover">
                </div>
            @endif
        </div>
    </div>
</div>
