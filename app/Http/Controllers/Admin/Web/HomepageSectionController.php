<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\HomepageSectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomepageSectionController extends Controller
{
    public function __construct(
        private readonly HomepageSectionService $homepageSections,
    ) {}

    public function index()
    {
        $sections = HomepageSection::query()
            ->with(['category:id,name_en', 'subcategory:id,name_en', 'productTypeDefinition:id,name'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('admin.homepage-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.homepage-sections.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        try {
            HomepageSection::query()->create($data);
            $this->homepageSections->clearCache();

            return redirect()->route('admin.homepage-sections.index')->with('success', 'Homepage section created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create homepage section.');
        }
    }

    public function edit(HomepageSection $homepageSection)
    {
        return view('admin.homepage-sections.edit', [
            'homepageSection' => $homepageSection,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, HomepageSection $homepageSection)
    {
        $data = $this->validatedData($request);

        try {
            $homepageSection->update($data);
            $this->homepageSections->clearCache();

            return redirect()->route('admin.homepage-sections.index')->with('success', 'Homepage section updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update homepage section.');
        }
    }

    public function destroy(HomepageSection $homepageSection)
    {
        try {
            $homepageSection->delete();
            $this->homepageSections->clearCache();

            return redirect()->route('admin.homepage-sections.index')->with('success', 'Homepage section deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete homepage section.');
        }
    }

    private function formData(): array
    {
        return [
            'types' => HomepageSection::typeOptions(),
            'sources' => HomepageSection::sourceOptions(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']),
            'subcategories' => Subcategory::query()
                ->where('is_active', true)
                ->with('category:id,name_en')
                ->orderBy('name_en')
                ->get(['id', 'category_id', 'name_en']),
            'products' => Product::query()
                ->where('is_active', true)
                ->with(['subcategory:id,category_id,name_en,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema'])
                ->orderBy('name_en')
                ->get(['id', 'subcategory_id', 'name_en', 'slug', 'selling_price']),
        ];
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(HomepageSection::typeOptions()))],
            'source_type' => ['required', 'string', Rule::in(array_keys(HomepageSection::sourceOptions()))],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ar' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,id'],
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:subcategories,id'],
            'product_type_id' => ['nullable', 'integer', 'exists:product_types,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'limit' => ['required', 'integer', 'min:1', 'max:24'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'settings.badge_en' => ['nullable', 'string', 'max:50'],
            'settings.badge_ar' => ['nullable', 'string', 'max:50'],
            'settings.button_text_en' => ['nullable', 'string', 'max:80'],
            'settings.button_text_ar' => ['nullable', 'string', 'max:80'],
            'settings.button_url' => ['nullable', 'string', 'max:255'],
            'settings.status_en' => ['nullable', 'string', 'max:80'],
            'settings.status_ar' => ['nullable', 'string', 'max:80'],
            'settings.amount_label_en' => ['nullable', 'string', 'max:80'],
            'settings.amount_label_ar' => ['nullable', 'string', 'max:80'],
            'settings.card_brand_en' => ['nullable', 'string', 'max:80'],
            'settings.card_brand_ar' => ['nullable', 'string', 'max:80'],
            'settings.card_kind_en' => ['nullable', 'string', 'max:80'],
            'settings.card_kind_ar' => ['nullable', 'string', 'max:80'],
            'settings.card_holder_en' => ['nullable', 'string', 'max:80'],
            'settings.card_holder_ar' => ['nullable', 'string', 'max:80'],
            'settings.features' => ['nullable', 'array'],
            'settings.features.*.icon' => ['nullable', 'string', 'max:60'],
            'settings.features.*.label_en' => ['nullable', 'string', 'max:120'],
            'settings.features.*.label_ar' => ['nullable', 'string', 'max:120'],
            'settings.features.*.text_en' => ['nullable', 'string', 'max:160'],
            'settings.features.*.text_ar' => ['nullable', 'string', 'max:160'],
            'settings.cards' => ['nullable', 'array'],
            'settings.cards.*.icon' => ['nullable', 'string', 'max:60'],
            'settings.cards.*.title_en' => ['nullable', 'string', 'max:120'],
            'settings.cards.*.title_ar' => ['nullable', 'string', 'max:120'],
            'settings.cards.*.text_en' => ['nullable', 'string', 'max:180'],
            'settings.cards.*.text_ar' => ['nullable', 'string', 'max:180'],
            'settings.cards.*.url' => ['nullable', 'string', 'max:255'],
            'settings.cards.*.accent' => ['nullable', 'string', 'max:30'],
        ]);

        if ($this->isContentType($data['type'])) {
            $data['source_type'] = HomepageSection::SOURCE_CONTENT_BLOCK;
        } elseif ($data['source_type'] === HomepageSection::SOURCE_CONTENT_BLOCK) {
            $data['type'] = HomepageSection::TYPE_TRUST_PAYMENTS;
        } else {
            $data['type'] = $this->typeForSource($data['source_type']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['product_ids'] = array_values($data['product_ids'] ?? []);
        $data['subcategory_ids'] = array_values($data['subcategory_ids'] ?? []);
        $data['settings'] = $this->cleanSettings($data['settings'] ?? []);

        if ($data['source_type'] === HomepageSection::SOURCE_CONTENT_BLOCK) {
            $data['product_ids'] = [];
            $data['subcategory_ids'] = [];
            $data['category_id'] = null;
            $data['subcategory_id'] = null;
            $data['product_type_id'] = null;
        } elseif (! in_array($data['source_type'], [HomepageSection::SOURCE_MANUAL_PRODUCTS, HomepageSection::SOURCE_SUBCATEGORY], true)) {
            $data['product_ids'] = [];
        }

        if ($data['source_type'] !== HomepageSection::SOURCE_SUBCATEGORIES) {
            $data['subcategory_ids'] = [];
        }

        return $data;
    }

    private function typeForSource(string $sourceType): string
    {
        return match ($sourceType) {
            HomepageSection::SOURCE_BEST_SELLER => HomepageSection::TYPE_BEST_SELLER,
            HomepageSection::SOURCE_FEATURED => HomepageSection::TYPE_TOP_DEAL,
            HomepageSection::SOURCE_LATEST => HomepageSection::TYPE_NEW_ARRIVALS,
            HomepageSection::SOURCE_SUBCATEGORY, HomepageSection::SOURCE_SUBCATEGORIES => HomepageSection::TYPE_CATEGORY_SHOWCASE,
            default => HomepageSection::TYPE_MANUAL_PRODUCTS,
        };
    }

    private function isContentType(string $type): bool
    {
        return in_array($type, [
            HomepageSection::TYPE_TRUST_PAYMENTS,
            HomepageSection::TYPE_SHOP_BY_NEED,
            HomepageSection::TYPE_CRYPTO_CARD,
            HomepageSection::TYPE_HOW_IT_WORKS,
        ], true);
    }

    private function cleanSettings(array $settings): array
    {
        $settings = array_filter($settings, fn ($value) => $value !== null && $value !== '' && $value !== []);

        foreach (['features', 'cards'] as $group) {
            if (! isset($settings[$group]) || ! is_array($settings[$group])) {
                continue;
            }

            $settings[$group] = array_values(array_filter(array_map(
                fn (array $row) => array_filter($row, fn ($value) => $value !== null && $value !== ''),
                $settings[$group]
            ), fn (array $row) => $row !== []));
        }

        return $settings;
    }
}
