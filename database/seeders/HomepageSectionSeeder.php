<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $this->removeOldDemoSections();

        $featuredProductIds = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(8)
            ->pluck('id')
            ->all();

        $fallbackProductIds = Product::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(8)
            ->pluck('id')
            ->all();

        $manualProductIds = $featuredProductIds ?: $fallbackProductIds;

        $subcategories = Subcategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->with('category:id,name_en,name_ar')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(4)
            ->get();

        $primarySubcategory = $subcategories->first();

        $this->upsertSection([
            'type' => HomepageSection::TYPE_TOP_DEAL,
            'source_type' => HomepageSection::SOURCE_FEATURED,
            'title_en' => 'Featured Picks',
            'title_ar' => 'مختارات مميزة',
            'subtitle_en' => 'Featured products selected for the homepage.',
            'subtitle_ar' => 'منتجات مميزة مختارة للصفحة الرئيسية.',
            'settings' => [
                'badge_en' => 'Featured',
                'badge_ar' => 'مميز',
            ],
            'limit' => 8,
            'sort_order' => 10,
        ]);

        $this->upsertSection([
            'type' => HomepageSection::TYPE_BEST_SELLER,
            'source_type' => HomepageSection::SOURCE_BEST_SELLER,
            'title_en' => 'Best Sellers',
            'title_ar' => 'الأكثر مبيعاً',
            'subtitle_en' => 'Products customers are buying most.',
            'subtitle_ar' => 'المنتجات الأكثر طلباً من العملاء.',
            'settings' => [
                'badge_en' => 'Trending',
                'badge_ar' => 'رائج',
            ],
            'limit' => 8,
            'sort_order' => 20,
        ]);

        if ($manualProductIds) {
            $this->upsertSection([
                'type' => HomepageSection::TYPE_MANUAL_PRODUCTS,
                'source_type' => HomepageSection::SOURCE_MANUAL_PRODUCTS,
                'title_en' => 'Selected Deals',
                'title_ar' => 'عروض مختارة',
                'subtitle_en' => 'Exact products selected by the admin.',
                'subtitle_ar' => 'منتجات محددة يدوياً من لوحة التحكم.',
                'product_ids' => $manualProductIds,
                'settings' => [
                    'badge_en' => 'Picked',
                    'badge_ar' => 'مختار',
                ],
                'limit' => 8,
                'sort_order' => 30,
            ]);
        }

        if ($primarySubcategory) {
            $this->upsertSection([
                'type' => HomepageSection::TYPE_CATEGORY_SHOWCASE,
                'source_type' => HomepageSection::SOURCE_SUBCATEGORY,
                'title_en' => 'Vault Picks',
                'title_ar' => 'مختارات القسم',
                'subtitle_en' => 'Products from one selected subcategory.',
                'subtitle_ar' => 'منتجات من قسم فرعي محدد.',
                'category_id' => $primarySubcategory->category_id,
                'subcategory_id' => $primarySubcategory->id,
                'settings' => [
                    'badge_en' => $primarySubcategory->name_en,
                    'badge_ar' => $primarySubcategory->name_ar,
                ],
                'limit' => 8,
                'sort_order' => 40,
            ]);
        }

        if ($subcategories->count() > 1) {
            $this->upsertSection([
                'type' => HomepageSection::TYPE_CATEGORY_SHOWCASE,
                'source_type' => HomepageSection::SOURCE_SUBCATEGORIES,
                'title_en' => 'Multi Vault Deals',
                'title_ar' => 'عروض متعددة الأقسام',
                'subtitle_en' => 'Products from multiple selected subcategories.',
                'subtitle_ar' => 'منتجات من عدة أقسام فرعية محددة.',
                'subcategory_ids' => $subcategories->pluck('id')->all(),
                'settings' => [
                    'badge_en' => 'Multi Vault',
                    'badge_ar' => 'أقسام متعددة',
                ],
                'limit' => 8,
                'sort_order' => 50,
            ]);
        }

        Cache::forget('storefront:homepage-section-ids');
        Cache::forget('storefront:homepage-sections');
    }

    private function removeOldDemoSections(): void
    {
        HomepageSection::query()
            ->whereIn('title_en', [
                'Flash Sales',
                'Top Deals',
                'Featured Category',
            ])
            ->delete();
    }

    private function upsertSection(array $attributes): void
    {
        HomepageSection::query()->updateOrCreate(
            ['title_en' => $attributes['title_en']],
            array_merge([
                'title_ar' => $attributes['title_ar'],
                'subtitle_en' => null,
                'subtitle_ar' => null,
                'category_id' => null,
                'subcategory_id' => null,
                'subcategory_ids' => null,
                'product_type_id' => null,
                'product_ids' => null,
                'settings' => null,
                'limit' => 8,
                'sort_order' => 0,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ], $attributes)
        );
    }
}
