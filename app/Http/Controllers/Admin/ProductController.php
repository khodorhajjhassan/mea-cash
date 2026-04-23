<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\Media\ImageStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with(['subcategory:id,name_en,name_ar,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name'])
            ->latest('id')
            ->paginate(20)
            ->through(fn (Product $product): array => $this->transformProduct($product));

        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);
        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/products');
        }

        $product = Product::query()->create($data);

        return response()->json($this->transformProduct($product->load(['subcategory:id,name_en,name_ar,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name'])), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($this->transformProduct($product->load(['subcategory:id,name_en,name_ar,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name'])));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['slug']) || isset($data['name_en'])) {
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $product->name_en);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp(
                $request->file('image'),
                'catalog/products',
                $product->image
            );
        }

        $product->update($data);

        return response()->json($this->transformProduct($product->fresh()->load(['subcategory:id,name_en,name_ar,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name'])));
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->imageStorage->delete($product->image);
        $product->delete();

        return response()->json(status: 204);
    }

    private function transformProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'subcategory_id' => $product->subcategory_id,
            'subcategory' => $product->subcategory,
            'supplier_id' => $product->supplier_id,
            'supplier' => $product->supplier,
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'description_ar' => $product->description_ar,
            'description_en' => $product->description_en,
            'slug' => $product->slug,
            'product_type' => $product->resolvedProductType()->value,
            'product_type_label' => $product->resolvedProductTypeLabel(),
            'delivery_type' => $product->delivery_type,
            'delivery_time_minutes' => $product->delivery_time_minutes,
            'cost_price' => $product->cost_price,
            'selling_price' => $product->selling_price,
            'price_per_unit' => $product->price_per_unit,
            'min_quantity' => $product->min_quantity,
            'max_quantity' => $product->max_quantity,
            'image' => $product->image,
            'image_url' => $this->imageStorage->url($product->image),
            'is_active' => $product->is_active,
            'is_featured' => $product->is_featured,
            'stock_alert_threshold' => $product->stock_alert_threshold,
            'seo_title' => $product->seo_title,
            'seo_description' => $product->seo_description,
            'seo_keywords' => $product->seo_keywords,
            'sort_order' => $product->sort_order,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }
}
