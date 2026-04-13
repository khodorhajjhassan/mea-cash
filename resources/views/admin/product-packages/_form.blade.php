@php($editing = isset($productPackage))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field"><label>Product</label><select name="product_id" required>@foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id', $productPackage->product_id ?? null) == $product->id)>{{ $product->name_en }}</option>@endforeach</select></div>
    <div class="field"><label>Name (EN)</label><input type="text" name="name_en" value="{{ old('name_en', $productPackage->name_en ?? '') }}" required></div>
    <div class="field"><label>Name (AR)</label><input type="text" name="name_ar" value="{{ old('name_ar', $productPackage->name_ar ?? '') }}" required></div>
    <div class="field"><label>Amount</label><input type="number" step="0.0001" min="0.0001" name="amount" value="{{ old('amount', $productPackage->amount ?? 0) }}" required></div>
    <div class="field"><label>Cost Price</label><input type="number" step="0.0001" min="0" name="cost_price" value="{{ old('cost_price', $productPackage->cost_price ?? 0) }}"></div>
    <div class="field"><label>Selling Price</label><input type="number" step="0.0001" min="0" name="selling_price" value="{{ old('selling_price', $productPackage->selling_price ?? 0) }}"></div>
    <div class="field"><label>Badge Text</label><input type="text" name="badge_text" value="{{ old('badge_text', $productPackage->badge_text ?? '') }}"></div>
    <div class="field"><label>Sort Order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $productPackage->sort_order ?? 0) }}"></div>
    <div class="field"><label>Status</label><select name="is_available"><option value="1" @selected(old('is_available', $productPackage->is_available ?? true)==1)>Available</option><option value="0" @selected(old('is_available', $productPackage->is_available ?? true)==0)>Unavailable</option></select></div>
</div>
<div class="field mt-4"><label>Image</label><input type="file" name="image" accept="image/*">@if($editing && $productPackage->image)<p class="hint">Uploading a new image will replace the current one.</p>@endif</div>
