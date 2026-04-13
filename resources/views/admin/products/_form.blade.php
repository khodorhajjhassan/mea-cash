@php($editing = isset($product))
@php($uiType = old('product_type', match($product->product_type ?? 'fixed_package') {
    'custom_quantity' => 'top_up',
    'account_topup' => 'account',
    default => 'key',
}))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field"><label>Subcategory</label><select name="subcategory_id" required>@foreach($subcategories as $sub)<option value="{{ $sub->id }}" @selected(old('subcategory_id', $product->subcategory_id ?? null)==$sub->id)>{{ $sub->name_en }}</option>@endforeach</select></div>
    <div class="field"><label>Supplier</label><select name="supplier_id"><option value="">None</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? null)==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
    <div class="field"><label>Name (EN)</label><input id="product-name-en" type="text" name="name_en" value="{{ old('name_en', $product->name_en ?? '') }}" required></div>
    <div class="field"><label>Name (AR)</label><input type="text" name="name_ar" value="{{ old('name_ar', $product->name_ar ?? '') }}" required></div>
    <div class="field"><label>Slug</label><input id="product-slug" type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}"></div>
    <div class="field"><label>Product Type</label><select name="product_type" required><option value="top_up" @selected($uiType==='top_up')>Top Up</option><option value="key" @selected($uiType==='key')>Key</option><option value="account" @selected($uiType==='account')>Account</option></select></div>
    <div class="field"><label>Delivery Type</label><select name="delivery_type" required>@foreach(['instant','timed','manual'] as $type)<option value="{{ $type }}" @selected(old('delivery_type', $product->delivery_type ?? 'instant')===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
    <div class="field"><label>Delivery Time Minutes</label><input type="number" min="1" name="delivery_time_minutes" value="{{ old('delivery_time_minutes', $product->delivery_time_minutes ?? '') }}"></div>
    <div class="field"><label>Cost Price</label><input type="number" step="0.0001" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}"></div>
    <div class="field"><label>Selling Price</label><input type="number" step="0.0001" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? 0) }}"></div>
    <div class="field"><label>Price per Unit</label><input type="number" step="0.0001" min="0" name="price_per_unit" value="{{ old('price_per_unit', $product->price_per_unit ?? '') }}"></div>
    <div class="field"><label>Min Quantity</label><input type="number" min="1" name="min_quantity" value="{{ old('min_quantity', $product->min_quantity ?? 1) }}"></div>
    <div class="field"><label>Max Quantity</label><input type="number" min="1" name="max_quantity" value="{{ old('max_quantity', $product->max_quantity ?? '') }}"></div>
    <div class="field"><label>Stock Alert Threshold</label><input type="number" min="0" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold ?? 5) }}"></div>
    <div class="field"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"></div>
    <div class="field"><label>Status</label><select name="is_active"><option value="1" @selected(old('is_active', $product->is_active ?? true)==1)>Active</option><option value="0" @selected(old('is_active', $product->is_active ?? true)==0)>Disabled</option></select></div>
    <div class="field"><label>Featured</label><select name="is_featured"><option value="1" @selected(old('is_featured', $product->is_featured ?? false)==1)>Yes</option><option value="0" @selected(old('is_featured', $product->is_featured ?? false)==0)>No</option></select></div>
    <div class="field"><label>Image</label><input type="file" name="image" accept="image/*">@if($editing && $product->image)<p class="hint">Uploading a new image will replace the current one.</p>@endif</div>
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
