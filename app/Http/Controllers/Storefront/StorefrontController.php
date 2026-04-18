<?php
 
namespace App\Http\Controllers\Storefront;
 
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Banner;
use App\Models\Faq;
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
 
        $subcategoriesQuery = Subcategory::query()
            ->where('is_active', true)
            ->with(['category', 'products.packages'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order');
 
        // Subcategory filter
        if ($request->filled('subcategory')) {
            $subSlug = $request->input('subcategory');
            $subcategory = Subcategory::where('slug', $subSlug)->first();
            if ($subcategory) {
                $productsQuery->where('subcategory_id', $subcategory->id);
            }
        }

        // Category filter
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $category = Category::where('slug', $catSlug)->first();
            if ($category) {
                $subcategoryIds = Subcategory::where('category_id', $category->id)->pluck('id');
                $productsQuery->whereIn('subcategory_id', $subcategoryIds);
            }
        }
 
        // Featured filter
        if ($request->boolean('featured')) {
            $subcategoriesQuery->where('is_featured', true);
        }

        // Search
        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $needle = '%'.Str::lower(trim($q)).'%';
            $subcategoriesQuery->where(function ($nested) use ($needle) {
                $nested->whereRaw("LOWER(COALESCE(name_en, '')) LIKE ?", [$needle])
                    ->orWhereRaw("LOWER(COALESCE(name_ar, '')) LIKE ?", [$needle]);
            });
        }
 
        $products = $subcategoriesQuery->paginate(12); // Keeping variable name 'products' for blade compatibility if needed, but it's subs now
 
        if ($request->ajax()) {
            return view('storefront.partials.product-grid-items', compact('products'))->render();
        }
 
        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
 
        $faqs = Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
 
        $seo = $this->seoService->forPage('MeaCash');
 
        return view('storefront.home', compact(
            'categories',
            'featuredSubcategories',
            'products',
            'banners',
            'faqs',
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
            ->paginate(12);
 
        if (request()->ajax() && request()->has('page')) {
            return view('storefront.partials.product-grid-items', compact('products'))->render();
        }
 
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
 
        $productsQuery = Product::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $this->applyCaseInsensitiveSearch($query, $q);
            })
            ->with(['subcategory.category', 'packages' => fn ($qr) => $qr->where('is_available', true)->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order');
 
        if ($request->expectsJson() || $request->ajax() && !$request->has('page')) {
            $products = $productsQuery->limit(10)->get();
            return response()->json([
                'results' => $products->map(fn (Product $product) => [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->{"name_{$locale}"} ?? $product->name_en,
                    'category_name' => $product->subcategory?->category?->{"name_{$locale}"} ?? 'Uncategorized',
                    'image' => $product->image ? (str_starts_with($product->image, 'http') ? $product->image : Storage::url($product->image)) : null,
                    'price' => (float) ($product->packages->first()?->selling_price ?? $product->selling_price),
                ])->values()
            ]);
        }
 
        $products = $productsQuery->paginate(12);
 
        if ($request->ajax() && $request->has('page')) {
            return view('storefront.partials.product-grid-items', compact('products'))->render();
        }
 
        $seo = $this->seoService->forPage("Search: {$q}");
 
        return view('storefront.home', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'featuredSubcategories' => collect(),
            'products' => $products,
            'seo' => $seo,
            'searchQuery' => $q,
            'banners' => Banner::where('is_active', true)->orderBy('sort_order')->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get(),
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
     * Subcategory JSON for modal (AJAX endpoint).
     */
    public function subcategoryJson(string $slug)
    {
        $locale = app()->getLocale();
 
        $subcategory = Subcategory::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'products' => fn($q) => $q->where('is_active', true)->with(['packages' => fn($pq) => $pq->where('is_available', true)->orderBy('sort_order')]),
            ])
            ->firstOrFail();

        return response()->json([
            'id' => $subcategory->id,
            'name' => $subcategory->{"name_{$locale}"},
            'description' => $subcategory->{"description_{$locale}"},
            'image' => (function() use ($subcategory) {
                if (!$subcategory->image) return null;
                return str_starts_with($subcategory->image, 'http') ? $subcategory->image : \Illuminate\Support\Facades\Storage::url($subcategory->image);
            })(),
            'category' => [
                'name' => $subcategory->category->{"name_{$locale}"},
                'slug' => $subcategory->category->slug,
            ],
            'products' => $subcategory->products->map(function($product) use ($locale) {
                $groupedForms = $product->getGroupedForms($locale);
                return [
                    'id' => $product->id,
                    'name' => $product->{"name_{$locale}"},
                    'description' => $product->{"description_{$locale}"},
                    'image' => (function() use ($product) {
                        if (!$product->image) return null;
                        return str_starts_with($product->image, 'http') ? $product->image : \Illuminate\Support\Facades\Storage::url($product->image);
                    })(),
                    'product_type' => $product->product_type?->value,
                    'delivery_type' => $product->delivery_type,
                    'is_featured' => $product->is_featured,
                    'selling_price' => (float) $product->selling_price,
                    'price_per_unit' => (float) ($product->price_per_unit ?? $product->selling_price),
                    'min_quantity' => (int) ($product->min_quantity ?? 1),
                    'max_quantity' => (int) ($product->max_quantity ?? 10),
                    'fields' => $groupedForms['fields'],
                    'forms' => $groupedForms['forms'],
                ];
            }),
        ]);
    }
}
