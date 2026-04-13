<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Media\ImageStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->latest('id')
            ->paginate(20)
            ->through(fn (Category $category): array => $this->transformCategory($category));

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/categories');
        }

        $category = Category::query()->create($data);

        return response()->json($this->transformCategory($category), 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($this->transformCategory($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['slug']) || isset($data['name_en'])) {
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $category->name_en);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp(
                $request->file('image'),
                'catalog/categories',
                $category->image
            );
        }

        $category->update($data);

        return response()->json($this->transformCategory($category->fresh()));
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->imageStorage->delete($category->image);
        $category->delete();

        return response()->json(status: 204);
    }

    private function transformCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name_ar' => $category->name_ar,
            'name_en' => $category->name_en,
            'slug' => $category->slug,
            'icon' => $category->icon,
            'image' => $category->image,
            'image_url' => $this->imageStorage->url($category->image),
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
            'seo_title' => $category->seo_title,
            'seo_description' => $category->seo_description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }
}
