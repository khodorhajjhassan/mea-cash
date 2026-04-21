<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomepageSectionService
{
    private const CACHE_SECTION_IDS = 'storefront:homepage-section-ids';
    private const CACHE_LEGACY_SECTIONS = 'storefront:homepage-sections';

    /**
     * @return Collection<int, array{section: HomepageSection, items: Collection}>
     */
    public function activeSections(): Collection
    {
        if (! Schema::hasTable('homepage_sections')) {
            return collect();
        }

        $sectionIds = Cache::remember(self::CACHE_SECTION_IDS, now()->addMinutes(5), fn () => HomepageSection::query()
            ->visible()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->all());

        if (empty($sectionIds)) {
            return collect();
        }

        $positions = array_flip($sectionIds);

        return HomepageSection::query()
            ->whereIn('id', $sectionIds)
            ->with(['category:id,name_en,name_ar,slug', 'subcategory:id,name_en,name_ar,slug', 'productTypeDefinition:id,name,key'])
            ->get()
            ->sortBy(fn (HomepageSection $section) => $positions[$section->id] ?? PHP_INT_MAX)
            ->map(fn (HomepageSection $section) => [
                'section' => $section,
                'items' => $this->resolveItems($section),
            ])
            ->filter(fn (array $payload) => $payload['section']->isContentBlock() || $payload['items']->isNotEmpty())
            ->values();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_SECTION_IDS);
        Cache::forget(self::CACHE_LEGACY_SECTIONS);
    }

    private function resolveItems(HomepageSection $section): Collection
    {
        if ($section->isContentBlock()) {
            return collect(['content']);
        }

        if ($section->type === HomepageSection::TYPE_CATEGORY_SHOWCASE) {
            return $this->resolveSubcategories($section);
        }

        return $this->resolveProducts($section);
    }

    private function resolveSubcategories(HomepageSection $section): Collection
    {
        $query = Subcategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn (Builder $productQuery) => $productQuery->where('is_active', true))
            ->with([
                'category',
                'products' => fn ($productQuery) => $productQuery->where('is_active', true)
                    ->with(['packages' => fn ($packageQuery) => $packageQuery->where('is_available', true)->orderBy('sort_order')])
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order'),
            ]);

        if ($section->category_id) {
            $query->where('category_id', $section->category_id);
        }

        $subcategoryIds = $this->selectedSubcategoryIds($section);
        if ($subcategoryIds->isNotEmpty()) {
            $query->whereIn('id', $subcategoryIds);
        }

        return $query->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit($section->limit)
            ->get();
    }

    private function resolveProducts(HomepageSection $section): Collection
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['subcategory.category', 'packages' => fn ($packageQuery) => $packageQuery->where('is_available', true)->orderBy('sort_order')]);

        $this->applySource($query, $section);
        $this->applyOrdering($query, $section);

        return $query->limit($section->limit)->get();
    }

    private function applySource(Builder $query, HomepageSection $section): void
    {
        match ($section->source_type) {
            HomepageSection::SOURCE_CATEGORY => $section->category_id
                ? $query->whereHas('subcategory', fn (Builder $subcategoryQuery) => $subcategoryQuery->where('category_id', $section->category_id))
                : $query->whereRaw('1 = 0'),
            HomepageSection::SOURCE_SUBCATEGORY => $section->subcategory_id
                ? $query->where('subcategory_id', $section->subcategory_id)
                : $query->whereRaw('1 = 0'),
            HomepageSection::SOURCE_SUBCATEGORIES => $this->applySubcategories($query, $section),
            HomepageSection::SOURCE_PRODUCT_TYPE => $section->product_type_id
                ? $query->where('product_type_id', $section->product_type_id)
                : $query->whereRaw('1 = 0'),
            HomepageSection::SOURCE_FEATURED => $query->where('is_featured', true),
            HomepageSection::SOURCE_MANUAL_PRODUCTS => $this->applyManualProducts($query, $section),
            default => null,
        };

        if ($section->source_type === HomepageSection::SOURCE_SUBCATEGORY && ! empty($section->product_ids)) {
            $this->applyManualProducts($query, $section);
        }
    }

    private function applySubcategories(Builder $query, HomepageSection $section): void
    {
        $ids = $this->selectedSubcategoryIds($section);

        if ($ids->isEmpty()) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->whereIn('subcategory_id', $ids);
    }

    private function applyManualProducts(Builder $query, HomepageSection $section): void
    {
        $ids = collect($section->product_ids ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($ids->isEmpty()) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->whereIn('id', $ids);

        $caseSql = $ids->map(fn (int $id, int $index) => "WHEN {$id} THEN {$index}")->implode(' ');
        $query->orderByRaw("CASE id {$caseSql} ELSE {$ids->count()} END");
    }

    private function selectedSubcategoryIds(HomepageSection $section): Collection
    {
        return collect($section->subcategory_ids ?? [])
            ->push($section->subcategory_id)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function applyOrdering(Builder $query, HomepageSection $section): void
    {
        if ($section->source_type === HomepageSection::SOURCE_BEST_SELLER || $section->type === HomepageSection::TYPE_BEST_SELLER) {
            $completed = OrderStatus::Completed->value;

            $query->leftJoin('orders', function ($join) use ($completed): void {
                $join->on('orders.product_id', '=', 'products.id')
                    ->where('orders.status', '=', $completed);
            })
                ->select('products.*', DB::raw('COALESCE(SUM(orders.quantity), 0) as sold_quantity'))
                ->groupBy('products.id')
                ->orderByDesc('sold_quantity')
                ->orderByDesc('products.is_featured')
                ->orderBy('products.sort_order');

            return;
        }

        if ($section->source_type === HomepageSection::SOURCE_LATEST || $section->type === HomepageSection::TYPE_NEW_ARRIVALS) {
            $query->latest('products.created_at');
            return;
        }

        if ($section->source_type !== HomepageSection::SOURCE_MANUAL_PRODUCTS) {
            $query->orderByDesc('products.is_featured')
                ->orderBy('products.sort_order')
                ->latest('products.id');
        }
    }
}
