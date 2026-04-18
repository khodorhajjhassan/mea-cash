<?php
 
namespace App\Http\Controllers\Admin\Web;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
 
class CategoryController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }
 
    public function index(Request $request)
    {
        $categories = Category::query()
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
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
 
        $filters = $request->only(['q', 'status']);
 
        return view('admin.categories.index', compact('categories', 'filters'));
    }
 
    public function create()
    {
        return view('admin.categories.create');
    }
 
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);
 
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/categories');
            }
 
            Category::query()->create($data);
 
            return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
        } catch (Exception $exception) {
            report($exception);
 
            return back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }
    }
 
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }
 
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
 
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();
 
            if (isset($data['slug']) || isset($data['name_en'])) {
                $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $category->name_en);
            }
 
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/categories', $category->image);
            }
 
            $category->update($data);
 
            return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
        } catch (Exception $exception) {
            report($exception);
 
            return back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }
    }
 
    public function destroy(Category $category)
    {
        try {
            $this->imageStorage->delete($category->image);
            $category->delete();
 
            return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
        } catch (Exception $exception) {
            report($exception);
 
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
 
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'exists:categories,id'],
            'orders.*.sort_order' => ['required', 'integer'],
        ]);
 
        foreach ($data['orders'] as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
 
        return response()->json(['success' => true]);
    }
}
