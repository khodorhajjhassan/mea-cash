<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPackageRequest;
use App\Http\Requests\UpdateProductPackageRequest;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Services\Media\ImageStorageService;
use Exception;

class ProductPackageController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index()
    {
        $packages = ProductPackage::query()->with('product:id,name_en')->latest('id')->paginate(15);

        return view('admin.product-packages.index', compact('packages'));
    }

    public function create()
    {
        $products = Product::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);

        return view('admin.product-packages.create', compact('products'));
    }

    public function store(StoreProductPackageRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/packages');
            }

            ProductPackage::query()->create($data);

            return redirect()->route('admin.product-packages.index')->with('success', 'Package created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create package. Please try again.');
        }
    }

    public function show(ProductPackage $productPackage)
    {
        $productPackage->load('product:id,name_en');

        return view('admin.product-packages.show', compact('productPackage'));
    }

    public function edit(ProductPackage $productPackage)
    {
        $products = Product::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);

        return view('admin.product-packages.edit', compact('productPackage', 'products'));
    }

    public function update(UpdateProductPackageRequest $request, ProductPackage $productPackage)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/packages', $productPackage->image);
            }

            $productPackage->update($data);

            return redirect()->route('admin.product-packages.index')->with('success', 'Package updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update package. Please try again.');
        }
    }

    public function destroy(ProductPackage $productPackage)
    {
        try {
            $this->imageStorage->delete($productPackage->image);
            $productPackage->delete();

            return redirect()->route('admin.product-packages.index')->with('success', 'Package deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete package. Please try again.');
        }
    }
}
