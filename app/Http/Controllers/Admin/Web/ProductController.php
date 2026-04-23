<?php
 
namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
 
class ProductController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    )
    {
    }
 
    public function index(Request $request)
    {
        $subcategories = Subcategory::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);
 
        $products = Product::query()
            ->with(['subcategory:id,name_en,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name'])
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
            ->when($request->filled('subcategory_id'), function ($query) use ($request): void {
                $query->where('subcategory_id', $request->input('subcategory_id'));
            })
            ->when($request->filled('type'), function ($query) use ($request): void {
                $type = (string) $request->string('type');
                $templateKeys = match ($type) {
                    'top_up' => ['quantity-or-price', 'biggoexample'],
                    'account' => ['account-id-required', 'account-login-credentials'],
                    'key' => ['package-only', 'final-package-only'],
                    default => [],
                };

                if ($templateKeys !== []) {
                    $query->whereHas('subcategory.productTypeDefinition', fn ($templateQuery) => $templateQuery->whereIn('key', $templateKeys));
                }
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
 
        $filters = $request->only(['q', 'status', 'type', 'subcategory_id']);
 
        return view('admin.products.index', compact('products', 'filters', 'subcategories'));
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
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name_en']);
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
        $product->load(['subcategory:id,name_en,product_type_id', 'subcategory.productTypeDefinition:id,name,key,schema', 'supplier:id,name']);
 
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
                $data['slug'] = $this->generateUniqueSlug(
                    $data['slug'] ?? $data['name_en'] ?? $product->name_en,
                    $product->id
                );
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
 
    public function duplicate(Product $product)
    {
        try {
            DB::transaction(function () use ($product): void {
                $copy = $product->replicate();
                $copy->name_en = $product->name_en.' Copy';
                $copy->name_ar = $product->name_ar.' Copy';
                $copy->slug = $this->generateUniqueSlug($product->slug ?: $product->name_en);
                $copy->is_active = false;
                $copy->is_featured = false;
                $copy->push();
            });
 
            return redirect()->route('admin.products.index')->with('success', 'Product duplicated successfully as inactive.');
        } catch (Exception $exception) {
            report($exception);
 
            return back()->with('error', 'Failed to duplicate product.');
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
 
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'exists:products,id'],
            'orders.*.sort_order' => ['required', 'integer'],
        ]);
 
        foreach ($data['orders'] as $item) {
            Product::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
 
        return response()->json(['success' => true]);
    }
 
    private function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'product';
        }
 
        $candidate = $base;
        $counter = 2;
 
        while (Product::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }
 
        return $candidate;
    }
}
