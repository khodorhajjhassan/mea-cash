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
    <label>SEO Title</label>
    <input type="text" name="seo_title" value="{{ old('seo_title', $category->seo_title ?? '') }}">
</div>
<div class="field mt-4">
    <label>SEO Description</label>
    <textarea name="seo_description" rows="3">{{ old('seo_description', $category->seo_description ?? '') }}</textarea>
</div>
<div class="field mt-4">
    <label>Image</label>
    <input type="file" name="image" accept="image/*">
    @if ($editing && $category->image)
        <p class="hint">Uploading a new image will replace the current one.</p>
    @endif
</div>
