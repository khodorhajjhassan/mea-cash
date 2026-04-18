@php
    $section = $section ?? null;
    $selectedProducts = old('product_ids', $section->product_ids ?? []);
    $selectedSubcategories = old('subcategory_ids', $section->subcategory_ids ?? []);
@endphp

<div class="grid gap-5 md:grid-cols-2" data-homepage-section-form>
    <div class="field">
        <label>Product Source</label>
        <select name="source_type" required data-section-source>
            @foreach($sources as $value => $label)
                <option value="{{ $value }}" @selected(old('source_type', $section->source_type ?? \App\Models\HomepageSection::SOURCE_MANUAL_PRODUCTS) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="hint">Choose how this section gets products. The section name is controlled by the title fields below.</p>
    </div>

    <div class="field">
        <label>Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $section->sort_order ?? 0) }}">
    </div>

    <div class="field">
        <label>Title (EN)</label>
        <input type="text" name="title_en" value="{{ old('title_en', $section->title_en ?? '') }}" required>
    </div>

    <div class="field">
        <label>Title (AR)</label>
        <input type="text" name="title_ar" dir="rtl" value="{{ old('title_ar', $section->title_ar ?? '') }}" required>
    </div>

    <div class="field">
        <label>Subtitle (EN)</label>
        <input type="text" name="subtitle_en" value="{{ old('subtitle_en', $section->subtitle_en ?? '') }}">
    </div>

    <div class="field">
        <label>Subtitle (AR)</label>
        <input type="text" name="subtitle_ar" dir="rtl" value="{{ old('subtitle_ar', $section->subtitle_ar ?? '') }}">
    </div>

    <div class="field" data-source-panel="manual_products subcategory">
        <label>Category Filter</label>
        <select name="category_id" data-category-filter>
            <option value="">Choose category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $section->category_id ?? 0) === $category->id)>{{ $category->name_en }}</option>
            @endforeach
        </select>
        <p class="hint">Optional helper for one-subcategory and selected-product sections.</p>
    </div>

    <div class="field" data-source-panel="subcategory manual_products">
        <label>Subcategory</label>
        <select name="subcategory_id" data-subcategory-filter>
            <option value="">Choose subcategory</option>
            @foreach($subcategories as $subcategory)
                <option value="{{ $subcategory->id }}" data-category="{{ $subcategory->category_id }}" @selected((int) old('subcategory_id', $section->subcategory_id ?? 0) === $subcategory->id)>
                    {{ $subcategory->name_en }}{{ $subcategory->category ? ' - '.$subcategory->category->name_en : '' }}
                </option>
            @endforeach
        </select>
        <p class="hint">For one-subcategory sections, leave products empty to show every active product in this subcategory.</p>
    </div>

    <div class="field">
        <label>Limit</label>
        <input type="number" name="limit" min="1" max="24" value="{{ old('limit', $section->limit ?? 8) }}" required>
    </div>

    <div class="field">
        <label>Badge (EN)</label>
        <input type="text" name="settings[badge_en]" value="{{ old('settings.badge_en', $section->settings['badge_en'] ?? '') }}" placeholder="Limited, Hot, New">
    </div>

    <div class="field">
        <label>Badge (AR)</label>
        <input type="text" name="settings[badge_ar]" dir="rtl" value="{{ old('settings.badge_ar', $section->settings['badge_ar'] ?? '') }}">
    </div>

    <div class="field">
        <label>Starts At</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($section?->starts_at)->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="field">
        <label>Ends At</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($section?->ends_at)->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="field md:col-span-2" data-source-panel="subcategories">
        <label>Multiple Subcategory Source</label>
        <input type="search" data-filter-list="subcategory_ids" placeholder="Search subcategories..." class="mb-3">
        <div class="max-h-72 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3" data-checkbox-list="subcategory_ids">
            @foreach($subcategories as $subcategory)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                    data-checkbox-row
                    data-category="{{ $subcategory->category_id }}"
                    data-search="{{ Str::lower($subcategory->name_en.' '.($subcategory->category?->name_en ?? '')) }}">
                    <input type="checkbox" name="subcategory_ids[]" value="{{ $subcategory->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        @checked(in_array($subcategory->id, array_map('intval', $selectedSubcategories), true))>
                    <span>{{ $subcategory->name_en }}{{ $subcategory->category ? ' - '.$subcategory->category->name_en : '' }}</span>
                </label>
            @endforeach
        </div>
        <p class="hint">Select subcategories from any category. This section will show all active products from all selected subcategories.</p>
    </div>

    <div class="field md:col-span-2" data-source-panel="manual_products subcategory">
        <label>Products</label>
        <input type="search" data-filter-list="product_ids" placeholder="Search products by name, slug, subcategory, type, or price..." class="mb-3">
        <div class="max-h-80 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3" data-checkbox-list="product_ids">
            @foreach($products as $product)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                    data-checkbox-row
                    data-subcategory="{{ $product->subcategory_id }}"
                    data-category="{{ $product->subcategory?->category_id }}"
                    data-search="{{ Str::lower($product->name_en.' '.$product->slug.' '.($product->subcategory?->name_en ?? '').' '.($product->product_type?->value ?? $product->product_type ?? '').' '.$product->selling_price) }}">
                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        @checked(in_array($product->id, array_map('intval', $selectedProducts), true))>
                    <span>{{ $product->name_en }} - {{ $product->subcategory?->name_en ?? 'No subcategory' }} - {{ str_replace('_', ' ', $product->product_type?->value ?? $product->product_type ?? 'product') }} - ${{ number_format((float) $product->selling_price, 2) }}</span>
                </label>
            @endforeach
        </div>
        <p class="hint">For Selected Products, choose the exact products. For One Subcategory, this is optional and narrows the section to selected products only.</p>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex cursor-pointer items-center">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $section->is_active ?? true))>
            <div class="relative h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-focus:outline-none"></div>
            <span class="ms-3 text-sm font-medium text-slate-900">Active</span>
        </label>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-homepage-section-form]').forEach((form) => {
            const sourceSelect = form.querySelector('[data-section-source]');
            const categoryFilter = form.querySelector('[data-category-filter]');
            const subcategoryFilter = form.querySelector('[data-subcategory-filter]');
            const panels = form.querySelectorAll('[data-source-panel]');
            const productSearch = form.querySelector('[data-filter-list="product_ids"]');
            const subcategorySearch = form.querySelector('[data-filter-list="subcategory_ids"]');
            const productRows = form.querySelectorAll('[data-checkbox-list="product_ids"] [data-checkbox-row]');
            const multiSubcategoryRows = form.querySelectorAll('[data-checkbox-list="subcategory_ids"] [data-checkbox-row]');

            const syncPanels = () => {
                panels.forEach((panel) => {
                    const sources = panel.dataset.sourcePanel.split(' ');
                    panel.classList.toggle('hidden', !sources.includes(sourceSelect.value));
                });
            };

            const syncOptions = () => {
                const categoryId = categoryFilter?.value || '';
                const subcategoryId = subcategoryFilter?.value || '';
                const productNeedle = productSearch?.value.trim().toLowerCase() || '';
                const subcategoryNeedle = subcategorySearch?.value.trim().toLowerCase() || '';

                form.querySelectorAll('select[name="subcategory_id"] option[data-category]').forEach((option) => {
                    const hiddenByCategory = Boolean(categoryId) && option.dataset.category !== categoryId;
                    option.hidden = hiddenByCategory;
                });

                multiSubcategoryRows.forEach((row) => {
                    const checked = row.querySelector('input')?.checked;
                    row.classList.toggle('hidden', !checked && Boolean(subcategoryNeedle) && !row.dataset.search.includes(subcategoryNeedle));
                });

                if (subcategoryFilter?.selectedOptions[0]?.hidden) {
                    subcategoryFilter.value = '';
                }

                productRows.forEach((row) => {
                    const checked = row.querySelector('input')?.checked;
                    const hiddenByCategory = Boolean(categoryId) && row.dataset.category !== categoryId;
                    const hiddenBySubcategory = Boolean(subcategoryId) && row.dataset.subcategory !== subcategoryId;
                    const hiddenBySearch = Boolean(productNeedle) && !row.dataset.search.includes(productNeedle);
                    row.classList.toggle('hidden', !checked && (hiddenByCategory || hiddenBySubcategory || hiddenBySearch));
                });
            };

            sourceSelect?.addEventListener('change', syncPanels);
            categoryFilter?.addEventListener('change', syncOptions);
            subcategoryFilter?.addEventListener('change', syncOptions);
            productSearch?.addEventListener('input', syncOptions);
            subcategorySearch?.addEventListener('input', syncOptions);
            productRows.forEach((row) => row.querySelector('input')?.addEventListener('change', syncOptions));
            multiSubcategoryRows.forEach((row) => row.querySelector('input')?.addEventListener('change', syncOptions));
            syncPanels();
            syncOptions();
        });
    });
</script>
@endpush
