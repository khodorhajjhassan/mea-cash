@php($editing = isset($product))
@php($selectedType = old('product_type', $product->product_type?->value ?? \App\Enums\ProductType::FixedPackage->value))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field"><label>Subcategory</label><select name="subcategory_id" required>@foreach($subcategories as $sub)<option value="{{ $sub->id }}" @selected(old('subcategory_id', $product->subcategory_id ?? null)==$sub->id)>{{ $sub->name_en }}</option>@endforeach</select></div>
    <div class="field"><label>Supplier</label><select name="supplier_id"><option value="">None</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? null)==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
    <div class="field"><label>Name (EN)</label><input id="product-name-en" type="text" name="name_en" value="{{ old('name_en', $product->name_en ?? '') }}" required></div>
    <div class="field"><label>Name (AR)</label><input type="text" name="name_ar" value="{{ old('name_ar', $product->name_ar ?? '') }}" required></div>
    <div class="field"><label>Slug</label><input id="product-slug" type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}"></div>
    <div class="field">
        <label>Product Type</label>
        <select name="product_type" required>
            @foreach(($typeOptions ?? \App\Enums\ProductType::options()) as $value => $label)
                <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>Delivery Type</label><select name="delivery_type" required>@foreach(['instant','timed','manual'] as $type)<option value="{{ $type }}" @selected(old('delivery_type', $product->delivery_type ?? 'instant')===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
    <div class="field"><label>Delivery Time Minutes</label><input type="number" min="1" name="delivery_time_minutes" value="{{ old('delivery_time_minutes', $product->delivery_time_minutes ?? '') }}"></div>
    <div class="field"><label>Cost Price</label><input type="number" step="0.0001" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}"></div>
    <div class="field"><label>Selling Price</label><input type="number" step="0.0001" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? 0) }}"></div>
    <div class="field"><label>Price per Unit</label><input type="number" step="0.0001" min="0" name="price_per_unit" value="{{ old('price_per_unit', $product->price_per_unit ?? '') }}"></div>
    <div class="field"><label>Stock Alert Threshold</label><input type="number" min="0" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold ?? 5) }}"></div>
    <div class="field"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"></div>
    <div class="field"><label>Status</label><select name="is_active"><option value="1" @selected(old('is_active', $product->is_active ?? true)==1)>Active</option><option value="0" @selected(old('is_active', $product->is_active ?? true)==0)>Disabled</option></select></div>
    <div class="field"><label>Featured</label><select name="is_featured"><option value="1" @selected(old('is_featured', $product->is_featured ?? false)==1)>Yes</option><option value="0" @selected(old('is_featured', $product->is_featured ?? false)==0)>No</option></select></div>
    <div class="field">
        <label>Image</label>
        <input type="file" name="image" accept="image/*">
        @if($editing && $product->image)
            <div class="mt-2 h-20 w-32 rounded border border-slate-200 overflow-hidden shadow-sm bg-white">
                <x-admin.image :path="$product->image" class="h-full w-full object-cover" />
            </div>
            <p class="hint">Uploading a new image will replace the current one.</p>
        @endif
    </div>
</div>

<div class="mt-8 border-t border-slate-100 pt-8" x-data="{ open: false }">
    <button type="button" @click="open = !open" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest text-xs font-bold mb-4">
        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        Search Engine Optimization (SEO)
    </button>
    
    <div x-show="open" x-cloak class="grid gap-4 md:grid-cols-2 bg-slate-50/50 p-6 rounded-2xl border border-slate-100 mb-6">
        <div class="field">
            <label>SEO Title</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $product->seo_title ?? '') }}" maxlength="70">
            <p class="text-[9px] text-slate-400 mt-1">Recommended: 60 characters. Leave empty to use product name.</p>
        </div>
        <div class="field">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords ?? '') }}" placeholder="gaming, cards, topup">
        </div>
        <div class="field md:col-span-2">
            <label>SEO Description</label>
            <textarea name="seo_description" rows="2" maxlength="160">{{ old('seo_description', $product->seo_description ?? '') }}</textarea>
            <p class="text-[9px] text-slate-400 mt-1">Recommended: 150-160 characters. Leave empty to use product description.</p>
        </div>
        <div class="field">
            <label>SEO Social Image</label>
            <input type="file" name="seo_image" accept="image/*" class="text-xs">
            @if($editing && $product->seo_image)
                <div class="mt-2 h-20 w-32 rounded border border-slate-200 overflow-hidden shadow-sm bg-white">
                    <x-admin.image :path="$product->seo_image" class="h-full w-full object-cover" />
                </div>
            @endif
        </div>
    </div>
</div>

<div class="field mt-4"><label>Description (EN)</label><textarea name="description_en" rows="3">{{ old('description_en', $product->description_en ?? '') }}</textarea></div>
<div class="field mt-4"><label>Description (AR)</label><textarea name="description_ar" rows="3">{{ old('description_ar', $product->description_ar ?? '') }}</textarea></div>
<div class="field mt-4"><label><input type="checkbox" name="force_apply_template" value="1" @checked(old('force_apply_template', true))> Apply selected template to dynamic form fields</label></div>
<script>
(function () {
    const nameInput = document.getElementById('product-name-en');
    const slugInput = document.getElementById('product-slug');
    if (!nameInput || !slugInput) return;

    const slugify = (value) => String(value || '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    let slugTouched = !!slugInput.value.trim();

    slugInput.addEventListener('input', () => {
        slugTouched = true;
        slugInput.value = slugify(slugInput.value);
    });

    nameInput.addEventListener('input', () => {
        if (!slugTouched || !slugInput.value.trim()) {
            slugInput.value = slugify(nameInput.value);
        }
    });
})();
</script>
