<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPackageRequest;
use App\Http\Requests\UpdateProductPackageRequest;
use App\Models\ProductPackage;
use App\Services\Media\ImageStorageService;
use Illuminate\Http\JsonResponse;

class ProductPackageController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(): JsonResponse
    {
        $packages = ProductPackage::query()
            ->with('product:id,name_en,name_ar')
            ->latest('id')
            ->paginate(20)
            ->through(fn (ProductPackage $package): array => $this->transformPackage($package));

        return response()->json($packages);
    }

    public function store(StoreProductPackageRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/packages');
        }

        $package = ProductPackage::query()->create($data);

        return response()->json($this->transformPackage($package->load('product:id,name_en,name_ar')), 201);
    }

    public function show(ProductPackage $productPackage): JsonResponse
    {
        return response()->json($this->transformPackage($productPackage->load('product:id,name_en,name_ar')));
    }

    public function update(UpdateProductPackageRequest $request, ProductPackage $productPackage): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp(
                $request->file('image'),
                'catalog/packages',
                $productPackage->image
            );
        }

        $productPackage->update($data);

        return response()->json($this->transformPackage($productPackage->fresh()->load('product:id,name_en,name_ar')));
    }

    public function destroy(ProductPackage $productPackage): JsonResponse
    {
        $this->imageStorage->delete($productPackage->image);
        $productPackage->delete();

        return response()->json(status: 204);
    }

    private function transformPackage(ProductPackage $package): array
    {
        return [
            'id' => $package->id,
            'product_id' => $package->product_id,
            'product' => $package->product,
            'name_ar' => $package->name_ar,
            'name_en' => $package->name_en,
            'amount' => $package->amount,
            'cost_price' => $package->cost_price,
            'selling_price' => $package->selling_price,
            'image' => $package->image,
            'image_url' => $this->imageStorage->url($package->image),
            'badge_text' => $package->badge_text,
            'is_available' => $package->is_available,
            'sort_order' => $package->sort_order,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at,
        ];
    }
}
