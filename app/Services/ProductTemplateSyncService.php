<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTemplateSyncService
{
    /**
     * Sync a single product's form fields from its template definition.
     *
     * This overwrites existing product form fields.
     */
    public function syncProduct(Product $product): void
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

        DB::transaction(function () use ($product, $globalFields, $forms): void {
            $product->formFields()->delete();

            $globalSort = 1;

            foreach ($globalFields as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }

                [$rawFieldKey, $fieldType, $isRequired, $rules] = $this->normalizeFieldBasics($field, $index);

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
                        'options' => is_array($field['options'] ?? null) ? $field['options'] : [],
                        'min' => isset($field['min']) && $field['min'] !== '' ? (int) $field['min'] : null,
                        'max' => isset($field['max']) && $field['max'] !== '' ? (int) $field['max'] : null,
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

                    [$rawFieldKey, $fieldType, $isRequired, $rules] = $this->normalizeFieldBasics($field, $index);

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
                            'options' => is_array($field['options'] ?? null) ? $field['options'] : [],
                            'min' => isset($field['min']) && $field['min'] !== '' ? (int) $field['min'] : null,
                            'max' => isset($field['max']) && $field['max'] !== '' ? (int) $field['max'] : null,
                        ],
                    ]);
                }
            }
        });
    }

    /**
     * Cascade sync: update all products under a subcategory.
     *
     * If $newProductTypeId is provided, products will have their product_type_id updated first.
     */
    public function syncSubcategoryProducts(Subcategory $subcategory, ?int $newProductTypeId = null): void
    {
        Product::query()
            ->where('subcategory_id', $subcategory->id)
            ->select(['id', 'subcategory_id', 'product_type_id'])
            ->orderBy('id')
            ->chunkById(200, function ($products) use ($subcategory, $newProductTypeId): void {
                /** @var Product $product */
                foreach ($products as $product) {
                    DB::transaction(function () use ($product, $subcategory, $newProductTypeId): void {
                        if ($newProductTypeId !== null) {
                            $product->update(['product_type_id' => $newProductTypeId]);
                        }

                        $product->unsetRelation('productTypeDefinition');
                        $product->setRelation('subcategory', $subcategory);
                        $this->syncProduct($product);
                    });
                }
            });
    }

    /**
     * Cascade sync: rebuild all products using the given product type.
     */
    public function syncProductsForProductType(ProductType $productType): void
    {
        Product::query()
            ->where('product_type_id', $productType->id)
            ->orderBy('id')
            ->chunkById(200, function ($products) use ($productType): void {
                /** @var Product $product */
                foreach ($products as $product) {
                    $product->setRelation('productTypeDefinition', $productType);
                    $this->syncProduct($product);
                }
            });
    }

    /**
     * @return array{0:string,1:string,2:bool,3:array}
     */
    private function normalizeFieldBasics(array $field, int $index): array
    {
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

        return [$rawFieldKey, $fieldType, $isRequired, $rules];
    }
}

