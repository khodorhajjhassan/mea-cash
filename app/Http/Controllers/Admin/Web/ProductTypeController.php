<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = ProductType::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('key', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['q', 'status']);

        return view('admin.product-types.index', compact('types', 'filters'));
    }

    public function create()
    {
        return view('admin.product-types.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        try {
            ProductType::query()->create($data);

            return redirect()->route('admin.product-types.index')->with('success', 'Product type created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create product type.');
        }
    }

    public function show(ProductType $productType)
    {
        return view('admin.product-types.show', compact('productType'));
    }

    public function edit(ProductType $productType)
    {
        return view('admin.product-types.edit', compact('productType'));
    }

    public function update(Request $request, ProductType $productType)
    {
        $data = $this->validatedData($request, $productType);

        try {
            $productType->update($data);

            return redirect()->route('admin.product-types.index')->with('success', 'Product type updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update product type.');
        }
    }

    public function destroy(ProductType $productType)
    {
        try {
            $productType->delete();

            return back()->with('success', 'Product type deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete product type.');
        }
    }

    private function validatedData(Request $request, ?ProductType $productType = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'schema' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $decoded = json_decode($validated['schema'], true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'schema' => 'Schema must be a valid JSON object or array.',
            ]);
        }

        if (array_is_list($decoded)) {
            $decoded = ['fields' => $decoded];
        }

        $decoded = $this->normalizeSchema($decoded);

        return [
            'name' => $validated['name'],
            'key' => $this->generateUniqueKey(
                $validated['key'] ?? null,
                $validated['name'],
                $productType?->id
            ),
            'description' => $validated['description'] ?? null,
            'schema' => $decoded,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];
    }

    private function normalizeSchema(array $schema): array
    {
        $fieldsInput = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];
        $fields = [];
        foreach ($fieldsInput as $index => $field) {
            if (!is_array($field)) {
                continue;
            }
            $fields[] = $this->normalizeField($field, $index);
        }

        $formsInput = is_array($schema['forms'] ?? null) ? $schema['forms'] : [];

        $forms = [];
        foreach ($formsInput as $index => $form) {
            if (!is_array($form)) {
                continue;
            }
            $forms[] = $this->normalizeForm($form, $index);
        }

        if ($forms !== [] && !collect($forms)->contains(fn ($form) => (bool) ($form['is_default'] ?? false))) {
            $forms[0]['is_default'] = true;
        }

        return [
            'fields' => $fields,
            'forms' => $forms,
        ];
    }

    private function normalizeForm(array $form, int $index): array
    {
        $labelEn = trim((string) ($form['label_en'] ?? $form['name_en'] ?? $form['label'] ?? 'Form '.($index + 1)));
        $labelAr = trim((string) ($form['label_ar'] ?? $form['name_ar'] ?? $labelEn));
        $key = Str::slug((string) ($form['key'] ?? $labelEn));
        if ($key === '') {
            $key = 'form_'.($index + 1);
        }

        $fieldsInput = is_array($form['fields'] ?? null) ? $form['fields'] : [];
        $fields = [];
        foreach ($fieldsInput as $fieldIndex => $field) {
            if (!is_array($field)) {
                continue;
            }
            $fields[] = $this->normalizeField($field, $fieldIndex);
        }

        return [
            'key' => $key,
            'label_en' => $labelEn,
            'label_ar' => $labelAr,
            'is_default' => (bool) ($form['is_default'] ?? false),
            'sort_order' => (int) ($form['sort_order'] ?? $index + 1),
            'fields' => $fields,
        ];
    }

    private function normalizeField(array $field, int $index): array
    {
        $labelEn = (string) ($field['label_en'] ?? $field['label'] ?? 'Field '.($index + 1));
        $labelAr = (string) ($field['label_ar'] ?? $labelEn);
        $fieldKey = Str::slug((string) ($field['key'] ?? ''));
        if ($fieldKey === '') {
            $fieldKey = Str::slug($labelEn);
        }
        if ($fieldKey === '') {
            $fieldKey = 'field_'.($index + 1);
        }

        $type = (string) ($field['type'] ?? 'text');
        if (!in_array($type, ['text', 'email', 'password', 'number', 'select'], true)) {
            $type = 'text';
        }

        $rules = is_array($field['rules'] ?? null) ? array_values(array_filter(array_map(
            fn ($rule) => trim((string) $rule),
            $field['rules']
        ))) : [];
        if (!in_array('required', $rules, true) && (bool) ($field['required'] ?? false)) {
            $rules[] = 'required';
        }

        return [
            'key' => $fieldKey,
            'label_en' => $labelEn,
            'label_ar' => $labelAr,
            'type' => $type,
            'required' => (bool) ($field['required'] ?? false),
            'placeholder_en' => (string) ($field['placeholder_en'] ?? $field['placeholder'] ?? ''),
            'placeholder_ar' => (string) ($field['placeholder_ar'] ?? ''),
            'min' => isset($field['min']) && $field['min'] !== '' ? (int) $field['min'] : null,
            'max' => isset($field['max']) && $field['max'] !== '' ? (int) $field['max'] : null,
            'rules' => $rules,
            'sort_order' => (int) ($field['sort_order'] ?? $index + 1),
        ];
    }

    private function generateUniqueKey(?string $providedKey, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($providedKey ?: $name);
        if ($base === '') {
            $base = 'product-type';
        }

        $candidate = $base;
        $counter = 2;

        while (ProductType::query()
            ->where('key', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
