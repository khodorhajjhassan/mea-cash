<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductFormBuilderService
{
    public function getFormFields(Product $product, string $locale): Collection
    {
        $isArabic = $locale === 'ar';

        return $product->formFields()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($field) use ($isArabic) {
                return [
                    'key' => $field->field_key,
                    'label' => $isArabic ? $field->label_ar : $field->label_en,
                    'type' => $field->field_type,
                    'placeholder' => $isArabic ? $field->placeholder_ar : $field->placeholder_en,
                    'required' => $field->is_required,
                    'rules' => $field->validation_rules ?? [],
                ];
            });
    }
}
