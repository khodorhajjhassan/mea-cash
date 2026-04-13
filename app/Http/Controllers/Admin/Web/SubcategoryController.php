<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Subcategory;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(Request $request)
    {
        $subcategories = Subcategory::query()
            ->with(['category:id,name_en', 'productTypeDefinition:id,name,key'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where(function ($inner) use ($q): void {
                        $inner->where('name_en', 'like', "%{$q}%")
                            ->orWhere('name_ar', 'like', "%{$q}%")
                            ->orWhere('slug', 'like', "%{$q}%");
                    })->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name_en', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->when($request->filled('featured'), function ($query) use ($request): void {
                $query->where('is_featured', $request->string('featured')->value() === 'yes');
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['q', 'status', 'featured']);

        return view('admin.subcategories.index', compact('subcategories', 'filters'));
    }

    public function create()
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);
        $productTypes = ProductType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'key']);

        return view('admin.subcategories.create', compact('categories', 'productTypes'));
    }

    public function store(StoreSubcategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/subcategories');
            }

            Subcategory::query()->create($data);

            return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create subcategory. Please try again.');
        }
    }

    public function show(Subcategory $subcategory)
    {
        $subcategory->load(['category:id,name_en', 'productTypeDefinition:id,name,key']);

        return view('admin.subcategories.show', compact('subcategory'));
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);
        $productTypes = ProductType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'key']);

        return view('admin.subcategories.edit', compact('subcategory', 'categories', 'productTypes'));
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory)
    {
        try {
            $data = $request->validated();

            if (isset($data['slug']) || isset($data['name_en'])) {
                $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $subcategory->name_en);
            }

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/subcategories', $subcategory->image);
            }

            $subcategory->update($data);

            return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update subcategory. Please try again.');
        }
    }

    public function destroy(Subcategory $subcategory)
    {
        try {
            $this->imageStorage->delete($subcategory->image);
            $subcategory->delete();

            return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete subcategory. Please try again.');
        }
    }
}
