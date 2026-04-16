<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\SeoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly SeoService $seoService,
    ) {}

    /**
     * Homepage: hero, featured categories, hot deals, product grid.
     */
    public function index(Request $request)
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $featuredSubcategories = Subcategory::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        $productsQuery = Product::query()
            ->where('is_active', true)
            ->with(['subcategory.category', 'packages' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order');

        // Category filter
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $category = Category::where('slug', $catSlug)->first();
            if ($category) {
                $subcategoryIds = Subcategory::where('category_id', $category->id)->pluck('id');
                $productsQuery->whereIn('subcategory_id', $subcategoryIds);
            }
        }

        // Search
        if ($request->filled('q')) {
            $this->applyCaseInsensitiveSearch($productsQuery, (string) $request->input('q'));
        }

        $products = $productsQuery->paginate(20);

        $seo = $this->seoService->forPage('MeaCash');

        return view('storefront.home', compact(
            'categories',
            'featuredSubcategories',
            'products',
            'seo',
        ));
    }

    /**
     * Category page: shows subcategories and products.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $products = Product::query()
            ->where('is_active', true)
            ->whereIn('subcategory_id', $subcategories->pluck('id'))
            ->with(['subcategory', 'packages' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->paginate(20);

        $seo = $this->seoService->forCategory($category);

        return view('storefront.category', compact(
            'category',
            'subcategories',
            'products',
            'seo',
        ));
    }

    /**
     * Search results (JSON for AJAX or full page).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $locale = app()->getLocale();

        $products = Product::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $this->applyCaseInsensitiveSearch($query, $q);
            })
            ->with(['subcategory.category', 'packages' => fn ($qr) => $qr->where('is_available', true)->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(20)
            ->get();

        if ($request->wantsJson()) {
            return response()->json(
                $products->map(fn (Product $product) => [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->{"name_{$locale}"} ?? $product->name_en,
                    'category' => $product->subcategory?->category?->{"name_{$locale}"},
                    'subcategory' => $product->subcategory?->{"name_{$locale}"},
                    'image' => $product->image ? Storage::url($product->image) : null,
                    'price' => (float) ($product->packages->first()?->selling_price ?? $product->selling_price),
                ])->values()
            );
        }

        $seo = $this->seoService->forPage("Search: {$q}");

        return view('storefront.home', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'featuredSubcategories' => collect(),
            'products' => $products,
            'seo' => $seo,
            'searchQuery' => $q,
        ]);
    }

    private function applyCaseInsensitiveSearch(Builder $query, string $rawQuery): void
    {
        $needle = '%'.Str::lower(trim($rawQuery)).'%';

        $query->where(function (Builder $nested) use ($needle): void {
            $nested->whereRaw("LOWER(COALESCE(name_en, '')) LIKE ?", [$needle])
                ->orWhereRaw("LOWER(COALESCE(name_ar, '')) LIKE ?", [$needle])
                ->orWhereRaw("LOWER(COALESCE(description_en, '')) LIKE ?", [$needle])
                ->orWhereRaw("LOWER(COALESCE(description_ar, '')) LIKE ?", [$needle]);
        });
    }

    /**
     * Product JSON for modal (AJAX endpoint).
     */
    public function productJson(string $slug)
    {
        $locale = app()->getLocale();

        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'subcategory.category',
                'packages' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order'),
            ])
            ->firstOrFail();

        $groupedForms = $product->getGroupedForms($locale);

        return response()->json([
            'id' => $product->id,
            'name' => $product->{"name_{$locale}"},
            'description' => $product->{"description_{$locale}"},
            'image' => $product->image ? \Illuminate\Support\Facades\Storage::url($product->image) : null,
            'product_type' => $product->product_type?->value,
            'delivery_type' => $product->delivery_type,
            'is_featured' => $product->is_featured,
            'selling_price' => (float) $product->selling_price,
            'price_per_unit' => (float) ($product->price_per_unit ?? $product->selling_price),
            'min_quantity' => (int) ($product->min_quantity ?? 1),
            'max_quantity' => (int) ($product->max_quantity ?? 10),
            'packages' => $product->packages->map(fn ($pkg) => [
                'id' => $pkg->id,
                'name' => $pkg->{"name_{$locale}"},
                'selling_price' => (float) $pkg->selling_price,
                'badge_text' => $pkg->badge_text,
            ]),
            'fields' => $groupedForms['fields'],
            'forms' => $groupedForms['forms'],
            'category' => $product->subcategory?->category ? [
                'name' => $product->subcategory->category->{"name_{$locale}"},
                'slug' => $product->subcategory->category->slug,
            ] : null,
            'subcategory' => $product->subcategory ? [
                'name' => $product->subcategory->{"name_{$locale}"},
            ] : null,
        ]);
    }
}
