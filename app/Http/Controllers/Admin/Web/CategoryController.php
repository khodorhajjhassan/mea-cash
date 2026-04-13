<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index()
    {
        $categories = Category::query()->latest('id')->paginate(15);

        return view('admin.categories.index', compact('categories'));
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
}
