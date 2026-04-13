<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use App\Models\Subcategory;
use App\Services\Media\ImageStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(): JsonResponse
    {
        $subcategories = Subcategory::query()
            ->with('category:id,name_en,name_ar')
            ->latest('id')
            ->paginate(20)
            ->through(fn (Subcategory $subcategory): array => $this->transformSubcategory($subcategory));

        return response()->json($subcategories);
    }

    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['name_en']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/subcategories');
        }

        $subcategory = Subcategory::query()->create($data);

        return response()->json($this->transformSubcategory($subcategory->load('category:id,name_en,name_ar')), 201);
    }

    public function show(Subcategory $subcategory): JsonResponse
    {
        return response()->json($this->transformSubcategory($subcategory->load('category:id,name_en,name_ar')));
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['slug']) || isset($data['name_en'])) {
            $data['slug'] = Str::slug($data['slug'] ?? $data['name_en'] ?? $subcategory->name_en);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->storeAsWebp(
                $request->file('image'),
                'catalog/subcategories',
                $subcategory->image
            );
        }

        $subcategory->update($data);

        return response()->json($this->transformSubcategory($subcategory->fresh()->load('category:id,name_en,name_ar')));
    }

    public function destroy(Subcategory $subcategory): JsonResponse
    {
        $this->imageStorage->delete($subcategory->image);
        $subcategory->delete();

        return response()->json(status: 204);
    }

    private function transformSubcategory(Subcategory $subcategory): array
    {
        return [
            'id' => $subcategory->id,
            'category_id' => $subcategory->category_id,
            'category' => $subcategory->category,
            'name_ar' => $subcategory->name_ar,
            'name_en' => $subcategory->name_en,
            'slug' => $subcategory->slug,
            'image' => $subcategory->image,
            'image_url' => $this->imageStorage->url($subcategory->image),
            'is_active' => $subcategory->is_active,
            'sort_order' => $subcategory->sort_order,
            'seo_title' => $subcategory->seo_title,
            'seo_description' => $subcategory->seo_description,
            'created_at' => $subcategory->created_at,
            'updated_at' => $subcategory->updated_at,
        ];
    }
}
