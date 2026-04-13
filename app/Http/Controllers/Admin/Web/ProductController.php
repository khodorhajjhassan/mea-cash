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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private readonly ImageStorageService $imageStorage)
    {
    }

    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['subcategory:id,name_en,product_type_id', 'subcategory.productTypeDefinition:id,name,key', 'supplier:id,name'])
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
                $query->where('product_type', $this->mapUiProductTypeToDb($request->string('type')->value()));
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
            $data['product_type'] = $this->mapUiProductTypeToDb($data['product_type']);
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name_en']);
            $subcategory = Subcategory::query()->findOrFail($data['subcategory_id']);
            $data['product_type_id'] = $subcategory->product_type_id;

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/products');
            }

            /** @var Product $product */
            $product = Product::query()->create($data);

            if (!empty($subcategory->product_type_id) && $request->boolean('force_apply_template', true)) {
                $this->syncFormFieldsFromTemplate($product);
            }

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
            if (isset($data['product_type'])) {
                $data['product_type'] = $this->mapUiProductTypeToDb($data['product_type']);
            }

            if (isset($data['slug']) || isset($data['name_en'])) {
                $data['slug'] = $this->generateUniqueSlug(
                    $data['slug'] ?? $data['name_en'] ?? $product->name_en,
                    $product->id
                );
            }

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageStorage->storeAsWebp($request->file('image'), 'catalog/products', $product->image);
            }

            if (array_key_exists('subcategory_id', $data)) {
                $subcategory = Subcategory::query()->findOrFail($data['subcategory_id']);
                $data['product_type_id'] = $subcategory->product_type_id;
            } else {
                $subcategory = $product->subcategory()->first(['id', 'product_type_id']);
                $data['product_type_id'] = $subcategory?->product_type_id;
            }

            $typeChanged = (int) $product->product_type_id !== (int) ($data['product_type_id'] ?? null);
            $product->update($data);

            if ($request->boolean('force_apply_template') || $typeChanged) {
                $this->syncFormFieldsFromTemplate($product->fresh());
            }

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

                $product->formFields()->get()->each(function ($field) use ($copy): void {
                    $fieldCopy = $field->replicate();
                    $fieldCopy->product_id = $copy->id;
                    $fieldCopy->save();
                });

                $this->syncFormFieldsFromTemplate($copy);
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

    private function syncFormFieldsFromTemplate(Product $product): void
    {
        $template = $product->productTypeDefinition;
        if (!$template) {
            $product->loadMissing('subcategory.productTypeDefinition');
            $template = $product->subcategory?->productTypeDefinition;
        }

        if (!$template) {
            return;
        }

        $schema = $template->schema;
        $globalFields = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];
        $forms = is_array($schema['forms'] ?? null) ? $schema['forms'] : [];

        if (!is_array($globalFields) || !is_array($forms)) {
            return;
        }

        $product->formFields()->delete();

        $globalSort = 1;
        foreach ($globalFields as $index => $field) {
            if (!is_array($field)) {
                continue;
            }

            $fieldType = (string) ($field['type'] ?? 'text');
            if (!in_array($fieldType, ['text', 'email', 'password', 'number', 'select'], true)) {
                $fieldType = 'text';
            }

            $isRequired = (bool) ($field['required'] ?? false);
            $rules = $field['rules'] ?? [];
            if (!is_array($rules)) {
                $rules = [];
            }
            if ($isRequired && !in_array('required', $rules, true)) {
                $rules[] = 'required';
            }

            $rawFieldKey = (string) ($field['key'] ?? 'field_'.$index);
            $rawFieldKey = Str::slug($rawFieldKey);
            if ($rawFieldKey === '') {
                $rawFieldKey = 'field_'.$index;
            }

            $product->formFields()->create([
                'field_key' => $rawFieldKey,
                'label_en' => (string) ($field['label_en'] ?? $field['label'] ?? Str::headline($rawFieldKey)),
                'label_ar' => (string) ($field['label_ar'] ?? $field['label'] ?? Str::headline($rawFieldKey)),
                'field_type' => $fieldType,
                'placeholder_en' => (string) ($field['placeholder_en'] ?? $field['placeholder'] ?? ''),
                'placeholder_ar' => (string) ($field['placeholder_ar'] ?? $field['placeholder'] ?? ''),
                'is_required' => $isRequired,
                'sort_order' => (int) ($field['sort_order'] ?? $globalSort++),
                'validation_rules' => $rules,
                'ui_meta' => [
                    'form_key' => null,
                    'form_label_en' => null,
                    'form_label_ar' => null,
                    'is_default_form' => false,
                    'raw_field_key' => $rawFieldKey,
                ],
            ]);
        }

        foreach ($forms as $formIndex => $form) {
            if (!is_array($form)) {
                continue;
            }

            $formKey = Str::slug((string) ($form['key'] ?? 'form_'.($formIndex + 1)));
            if ($formKey === '') {
                $formKey = 'form_'.($formIndex + 1);
            }
            $formLabelEn = (string) ($form['label_en'] ?? 'Form '.($formIndex + 1));
            $formLabelAr = (string) ($form['label_ar'] ?? $formLabelEn);
            $isDefaultForm = (bool) ($form['is_default'] ?? false);

            $fields = is_array($form['fields'] ?? null) ? $form['fields'] : [];
            foreach ($fields as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }

                $fieldType = (string) ($field['type'] ?? 'text');
                if (!in_array($fieldType, ['text', 'email', 'password', 'number', 'select'], true)) {
                    $fieldType = 'text';
                }

                $isRequired = (bool) ($field['required'] ?? false);
                $rules = $field['rules'] ?? [];
                if (!is_array($rules)) {
                    $rules = [];
                }
                if ($isRequired && !in_array('required', $rules, true)) {
                    $rules[] = 'required';
                }

                $rawFieldKey = (string) ($field['key'] ?? 'field_'.$index);
                $rawFieldKey = Str::slug($rawFieldKey);
                if ($rawFieldKey === '') {
                    $rawFieldKey = 'field_'.$index;
                }

                $product->formFields()->create([
                    'field_key' => $formKey.'__'.$rawFieldKey,
                    'label_en' => (string) ($field['label_en'] ?? $field['label'] ?? Str::headline($rawFieldKey)),
                    'label_ar' => (string) ($field['label_ar'] ?? $field['label'] ?? Str::headline($rawFieldKey)),
                    'field_type' => $fieldType,
                    'placeholder_en' => (string) ($field['placeholder_en'] ?? $field['placeholder'] ?? ''),
                    'placeholder_ar' => (string) ($field['placeholder_ar'] ?? $field['placeholder'] ?? ''),
                    'is_required' => $isRequired,
                    'sort_order' => (int) ($field['sort_order'] ?? $globalSort++),
                    'validation_rules' => $rules,
                    'ui_meta' => [
                        'form_key' => $formKey,
                        'form_label_en' => $formLabelEn,
                        'form_label_ar' => $formLabelAr,
                        'is_default_form' => $isDefaultForm,
                        'raw_field_key' => $rawFieldKey,
                    ],
                ]);
            }
        }
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

    private function mapUiProductTypeToDb(string $uiValue): string
    {
        return match ($uiValue) {
            'top_up' => 'custom_quantity',
            'key' => 'fixed_package',
            'account' => 'account_topup',
            default => 'fixed_package',
        };
    }
}
