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
                $meta = is_array($field->ui_meta) ? $field->ui_meta : [];
                return [
                    'key' => $field->field_key,
                    'raw_key' => (string) ($meta['raw_field_key'] ?? $field->field_key),
                    'form_key' => (string) ($meta['form_key'] ?? 'default'),
                    'form_label' => $isArabic
                        ? (string) ($meta['form_label_ar'] ?? $meta['form_label_en'] ?? 'Default')
                        : (string) ($meta['form_label_en'] ?? 'Default'),
                    'is_default_form' => (bool) ($meta['is_default_form'] ?? false),
                    'label' => $isArabic ? $field->label_ar : $field->label_en,
                    'type' => $field->field_type,
                    'placeholder' => $isArabic ? $field->placeholder_ar : $field->placeholder_en,
                    'required' => $field->is_required,
                    'rules' => $field->validation_rules ?? [],
                ];
            });
    }
}
