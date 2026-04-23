@php
    $section = $section ?? null;
    $selectedProducts = old('product_ids', $section->product_ids ?? []);
    $selectedSubcategories = old('subcategory_ids', $section->subcategory_ids ?? []);
    $settings = old('settings', $section->settings ?? []);
    $featureRows = $settings['features'] ?? [];
    $cardRows = $settings['cards'] ?? [];
    $contentTypes = [
        \App\Models\HomepageSection::TYPE_TRUST_PAYMENTS,
        \App\Models\HomepageSection::TYPE_SHOP_BY_NEED,
        \App\Models\HomepageSection::TYPE_CRYPTO_CARD,
        \App\Models\HomepageSection::TYPE_HOW_IT_WORKS,
    ];
@endphp

<div class="grid gap-5 md:grid-cols-2" data-homepage-section-form>
    <div class="field">
        <label>Section Layout</label>
        <select name="type" required data-section-type>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $section->type ?? \App\Models\HomepageSection::TYPE_MANUAL_PRODUCTS) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="hint">Choose product grids or editable content blocks such as trust, shop by need, and crypto card.</p>
    </div>

    <div class="field">
        <label>Product Source</label>
        <select name="source_type" required data-section-source>
            @foreach($sources as $value => $label)
                <option value="{{ $value }}" @selected(old('source_type', $section->source_type ?? \App\Models\HomepageSection::SOURCE_MANUAL_PRODUCTS) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="hint">Used for product sections. Content blocks will save as Content Block automatically.</p>
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
        <input type="text" name="settings[badge_en]" value="{{ old('settings.badge_en', $settings['badge_en'] ?? '') }}" placeholder="Limited, Hot, New">
    </div>

    <div class="field">
        <label>Badge (AR)</label>
        <input type="text" name="settings[badge_ar]" dir="rtl" value="{{ old('settings.badge_ar', $settings['badge_ar'] ?? '') }}">
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
                    data-search="{{ Str::lower($product->name_en.' '.$product->slug.' '.($product->subcategory?->name_en ?? '').' '.$product->resolvedProductTypeLabel().' '.$product->selling_price) }}">
                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        @checked(in_array($product->id, array_map('intval', $selectedProducts), true))>
                    <span>{{ $product->name_en }} - {{ $product->subcategory?->name_en ?? 'No subcategory' }} - {{ $product->resolvedProductTypeLabel() }} - ${{ number_format((float) $product->selling_price, 2) }}</span>
                </label>
            @endforeach
        </div>
        <p class="hint">For Selected Products, choose the exact products. For One Subcategory, this is optional and narrows the section to selected products only.</p>
    </div>

    <div class="md:col-span-2 grid gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2" data-content-panel="trust_payments shop_by_need crypto_card how_it_works">
        <div class="field">
            <label>Button Text (EN)</label>
            <input type="text" name="settings[button_text_en]" value="{{ old('settings.button_text_en', $settings['button_text_en'] ?? '') }}">
        </div>
        <div class="field">
            <label>Button Text (AR)</label>
            <input type="text" name="settings[button_text_ar]" dir="rtl" value="{{ old('settings.button_text_ar', $settings['button_text_ar'] ?? '') }}">
        </div>
        <div class="field md:col-span-2">
            <label>Button URL</label>
            <input type="text" name="settings[button_url]" value="{{ old('settings.button_url', $settings['button_url'] ?? '') }}" placeholder="/en#products-section">
        </div>
    </div>

    <div class="md:col-span-2 grid gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2" data-content-panel="crypto_card">
        <div class="field">
            <label>Status (EN)</label>
            <input type="text" name="settings[status_en]" value="{{ old('settings.status_en', $settings['status_en'] ?? '') }}" placeholder="Active">
        </div>
        <div class="field">
            <label>Status (AR)</label>
            <input type="text" name="settings[status_ar]" dir="rtl" value="{{ old('settings.status_ar', $settings['status_ar'] ?? '') }}">
        </div>
        <div class="field">
            <label>Amount Label (EN)</label>
            <input type="text" name="settings[amount_label_en]" value="{{ old('settings.amount_label_en', $settings['amount_label_en'] ?? '') }}" placeholder="$2,450">
        </div>
        <div class="field">
            <label>Amount Label (AR)</label>
            <input type="text" name="settings[amount_label_ar]" dir="rtl" value="{{ old('settings.amount_label_ar', $settings['amount_label_ar'] ?? '') }}">
        </div>
        <div class="field">
            <label>Card Brand (EN)</label>
            <input type="text" name="settings[card_brand_en]" value="{{ old('settings.card_brand_en', $settings['card_brand_en'] ?? '') }}" placeholder="MEACASH CARD">
        </div>
        <div class="field">
            <label>Card Brand (AR)</label>
            <input type="text" name="settings[card_brand_ar]" dir="rtl" value="{{ old('settings.card_brand_ar', $settings['card_brand_ar'] ?? '') }}">
        </div>
        <div class="field">
            <label>Card Type (EN)</label>
            <input type="text" name="settings[card_kind_en]" value="{{ old('settings.card_kind_en', $settings['card_kind_en'] ?? '') }}" placeholder="CRYPTO WALLET">
        </div>
        <div class="field">
            <label>Card Type (AR)</label>
            <input type="text" name="settings[card_kind_ar]" dir="rtl" value="{{ old('settings.card_kind_ar', $settings['card_kind_ar'] ?? '') }}">
        </div>
        <div class="field">
            <label>Card Holder (EN)</label>
            <input type="text" name="settings[card_holder_en]" value="{{ old('settings.card_holder_en', $settings['card_holder_en'] ?? '') }}" placeholder="MEACASH USER">
        </div>
        <div class="field">
            <label>Card Holder (AR)</label>
            <input type="text" name="settings[card_holder_ar]" dir="rtl" value="{{ old('settings.card_holder_ar', $settings['card_holder_ar'] ?? '') }}">
        </div>
    </div>

    <div class="field md:col-span-2" data-content-panel="trust_payments crypto_card how_it_works">
        <label>Feature / Step Items</label>
        <div class="grid gap-3">
            @for($i = 0; $i < 4; $i++)
                @php $row = $featureRows[$i] ?? []; @endphp
                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-3 md:grid-cols-5">
                    <input type="text" name="settings[features][{{ $i }}][icon]" value="{{ $row['icon'] ?? '' }}" placeholder="Icon name">
                    <input type="text" name="settings[features][{{ $i }}][label_en]" value="{{ $row['label_en'] ?? '' }}" placeholder="Label EN">
                    <input type="text" name="settings[features][{{ $i }}][label_ar]" dir="rtl" value="{{ $row['label_ar'] ?? '' }}" placeholder="Label AR">
                    <input type="text" name="settings[features][{{ $i }}][text_en]" value="{{ $row['text_en'] ?? '' }}" placeholder="Text EN">
                    <input type="text" name="settings[features][{{ $i }}][text_ar]" dir="rtl" value="{{ $row['text_ar'] ?? '' }}" placeholder="Text AR">
                </div>
            @endfor
        </div>
        <p class="hint">Use Google Material Symbols icon names, for example shield, bolt, public, wallet.</p>
    </div>

    <div class="field md:col-span-2" data-content-panel="shop_by_need">
        <label>Shop Cards</label>
        <div class="grid gap-3">
            @for($i = 0; $i < 6; $i++)
                @php $row = $cardRows[$i] ?? []; @endphp
                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-3 md:grid-cols-4">
                    <input type="text" name="settings[cards][{{ $i }}][icon]" value="{{ $row['icon'] ?? '' }}" placeholder="Icon">
                    <input type="text" name="settings[cards][{{ $i }}][accent]" value="{{ $row['accent'] ?? '' }}" placeholder="#00e5ff">
                    <input type="text" name="settings[cards][{{ $i }}][title_en]" value="{{ $row['title_en'] ?? '' }}" placeholder="Title EN">
                    <input type="text" name="settings[cards][{{ $i }}][title_ar]" dir="rtl" value="{{ $row['title_ar'] ?? '' }}" placeholder="Title AR">
                    <input type="text" name="settings[cards][{{ $i }}][text_en]" value="{{ $row['text_en'] ?? '' }}" placeholder="Text EN">
                    <input type="text" name="settings[cards][{{ $i }}][text_ar]" dir="rtl" value="{{ $row['text_ar'] ?? '' }}" placeholder="Text AR">
                    <input type="text" name="settings[cards][{{ $i }}][url]" value="{{ $row['url'] ?? '' }}" placeholder="/en?category=gift-cards#products-section" class="md:col-span-2">
                </div>
            @endfor
        </div>
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
            const typeSelect = form.querySelector('[data-section-type]');
            const categoryFilter = form.querySelector('[data-category-filter]');
            const subcategoryFilter = form.querySelector('[data-subcategory-filter]');
            const panels = form.querySelectorAll('[data-source-panel]');
            const productSearch = form.querySelector('[data-filter-list="product_ids"]');
            const subcategorySearch = form.querySelector('[data-filter-list="subcategory_ids"]');
            const productRows = form.querySelectorAll('[data-checkbox-list="product_ids"] [data-checkbox-row]');
            const multiSubcategoryRows = form.querySelectorAll('[data-checkbox-list="subcategory_ids"] [data-checkbox-row]');
            const contentPanels = form.querySelectorAll('[data-content-panel]');
            const contentTypes = @js($contentTypes);

            const syncPanels = () => {
                const isContent = contentTypes.includes(typeSelect.value);
                sourceSelect.closest('.field')?.classList.toggle('hidden', isContent);
                if (isContent) {
                    sourceSelect.value = 'content_block';
                } else if (sourceSelect.value === 'content_block') {
                    sourceSelect.value = 'manual_products';
                }

                panels.forEach((panel) => {
                    const sources = panel.dataset.sourcePanel.split(' ');
                    panel.classList.toggle('hidden', isContent || !sources.includes(sourceSelect.value));
                });

                contentPanels.forEach((panel) => {
                    const types = panel.dataset.contentPanel.split(' ');
                    panel.classList.toggle('hidden', !types.includes(typeSelect.value));
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
            typeSelect?.addEventListener('change', syncPanels);
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
