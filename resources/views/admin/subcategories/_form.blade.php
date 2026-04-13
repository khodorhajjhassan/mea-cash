@php($editing = isset($subcategory))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field">
        <label>Category</label>
        <select name="category_id" required>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $subcategory->category_id ?? null) == $cat->id)>{{ $cat->name_en }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>Name (EN)</label><input type="text" name="name_en" value="{{ old('name_en', $subcategory->name_en ?? '') }}" required></div>
    <div class="field"><label>Name (AR)</label><input type="text" name="name_ar" value="{{ old('name_ar', $subcategory->name_ar ?? '') }}" required></div>
    <div class="field"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $subcategory->slug ?? '') }}"></div>
    <div class="field"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $subcategory->sort_order ?? 0) }}"></div>
    <div class="field"><label>Status</label><select name="is_active"><option value="1" @selected(old('is_active', $subcategory->is_active ?? true)==1)>Active</option><option value="0" @selected(old('is_active', $subcategory->is_active ?? true)==0)>Disabled</option></select></div>
</div>
<div class="field mt-4"><label>SEO Title</label><input type="text" name="seo_title" value="{{ old('seo_title', $subcategory->seo_title ?? '') }}"></div>
<div class="field mt-4"><label>SEO Description</label><textarea name="seo_description" rows="3">{{ old('seo_description', $subcategory->seo_description ?? '') }}</textarea></div>
<div class="field mt-4"><label>Image</label><input type="file" name="image" accept="image/*">@if($editing && $subcategory->image)<p class="hint">Uploading a new image will replace the current one.</p>@endif</div>
