<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductFormField;
use App\Models\ProductPackage;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['subcategory:id,name_en', 'supplier:id,name'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name_en', 'like', "%{$q}%")
                        ->orWhere('name_ar', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->when($request->filled('type'), function ($query) use ($request): void {
                $query->where('product_type', $request->string('type')->value());
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['q', 'status', 'type']);

        return view('admin.products.index', compact('products', 'filters'));
    }

    public function create()
    {
        $subcategories = Subcategory::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('subcategories', 'suppliers'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/products');
            }

            Product::query()->create($data);

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create product. Please try again.');
        }
    }

    public function show(Product $product)
    {
        $product->load(['subcategory:id,name_en', 'supplier:id,name']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $subcategories = Subcategory::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'subcategories', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            if (isset($data['slug']) || isset($data['name_en'])) {
                $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $product->name_en);
            }

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/products', $product->image);
            }

            $product->update($data);

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->imageStorage->delete($product->image);
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete product. Please try again.');
        }
    }

    public function storePackage(\Illuminate\Http\Request $request, Product $product)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'is_available' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $product->packages()->create($data);

            return back()->with('success', 'Product package added successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to add product package.');
        }
    }

    public function updatePackage(\Illuminate\Http\Request $request, ProductPackage $package)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'is_available' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $package->update($data);

            return back()->with('success', 'Product package updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update product package.');
        }
    }

    public function storeField(\Illuminate\Http\Request $request, Product $product)
    {
        $data = $request->validate([
            'field_key' => ['required', 'string', 'max:100'],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,email,password,number,select'],
            'placeholder_en' => ['nullable', 'string', 'max:255'],
            'placeholder_ar' => ['nullable', 'string', 'max:255'],
            'is_required' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $product->formFields()->create($data + ['validation_rules' => []]);

            return back()->with('success', 'Product form field added successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to add form field.');
        }
    }
}
