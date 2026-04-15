<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Request;

class SeoService
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    /**
     * Generate SEO data for a standard page.
     */
    public function forPage(string $title, ?string $description = null, ?string $image = null, string $type = 'website'): array
    {
        $template = $this->settingsService->get('seo_title_template', '{page_title} — MeaCash');
        $resolvedTitle = str_replace('{page_title}', $title, $template);
        
        $data = [
            'title' => $resolvedTitle,
            'description' => $description ?? $this->settingsService->get('seo_default_description'),
            'keywords' => $this->settingsService->get('seo_default_keywords'),
            'image' => $image ? (str_starts_with($image, 'http') ? $image : asset('storage/' . $image)) : $this->getOgImageUrl(),
            'type' => $type,
            'url' => Request::fullUrl(),
            'canonical' => $this->settingsService->get('seo_canonical_domain', url('/')) . Request::getPathInfo(),
            'robots' => $this->settingsService->get('seo_robots_default', 'index, follow'),
            'site_name' => $this->settingsService->get('og_site_name', 'MeaCash'),
            'twitter_handle' => $this->settingsService->get('twitter_site_handle'),
            'locale' => app()->getLocale() === 'ar' ? $this->settingsService->get('og_locale_ar', 'ar_LB') : $this->settingsService->get('og_locale_en', 'en_US'),
        ];

        // Ensure we don't have double storage/storage
        if ($data['image'] && str_contains($data['image'], 'storage/storage')) {
            $data['image'] = str_replace('storage/storage', 'storage', $data['image']);
        }

        return $data;
    }

    /**
     * Generate SEO data for a product page.
     */
    public function forProduct(Product $product): array
    {
        $locale = app()->getLocale();
        $title = $product->seo_title ?: $product->{"name_{$locale}"};
        $description = $product->seo_description ?: $product->{"description_{$locale}"};
        $image = $product->seo_image ?: $product->image;

        return $this->forPage($title, $description, $image, 'product');
    }

    /**
     * Generate SEO data for a category or subcategory page.
     */
    public function forCategory($category): array
    {
        $locale = app()->getLocale();
        $title = $category->seo_title ?: $category->{"name_{$locale}"};
        $description = $category->seo_description ?: null;
        $image = $category->seo_image ?: $category->image;

        return $this->forPage($title, $description, $image);
    }

    /**
     * Build JSON-LD structured data.
     */
    public function buildJsonLd(array $seoData): string
    {
        $businessInfo = [
            '@context' => 'https://schema.org',
            '@type' => $this->settingsService->get('schema_business_type', 'OnlineStore'),
            'name' => $this->settingsService->get('schema_business_name', 'MeaCash'),
            'url' => url('/'),
            'logo' => $this->settingsService->get('schema_logo_url') ? asset('storage/' . $this->settingsService->get('schema_logo_url')) : null,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => $this->settingsService->get('schema_country', 'LB'),
            ],
            'priceRange' => '$$',
        ];

        return json_encode($businessInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Build hreflang tags if enabled.
     */
    public function buildHreflang(): array
    {
        if (!$this->settingsService->get('hreflang_enabled')) {
            return [];
        }

        $path = Request::getPathInfo();
        return [
            'ar' => url('/ar' . $path),
            'en' => url('/en' . $path),
        ];
    }
    
    /**
     * Get the default OG image URL.
     */
    private function getOgImageUrl(): ?string
    {
        $path = $this->settingsService->get('og_default_image');
        return $path ? asset('storage/' . $path) : null;
    }
}
