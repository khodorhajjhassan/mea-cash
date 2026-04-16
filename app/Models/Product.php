<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'supplier_id',
        'product_type_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'slug',
        'product_type',
        'delivery_type',
        'delivery_time_minutes',
        'cost_price',
        'selling_price',
        'price_per_unit',
        'min_quantity',
        'max_quantity',
        'image',
        'is_active',
        'is_featured',
        'stock_alert_threshold',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_image',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'product_type' => \App\Enums\ProductType::class,
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productTypeDefinition(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ProductPackage::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(ProductFormField::class);
    }

    public function codes(): HasMany
    {
        return $this->hasMany(ProductCode::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Group form fields by their ui_meta.form_key for modal rendering.
     *
     * Returns a structured object with two layers:
     *  - 'fields': Global fields (form_key is null) — always visible above tabs
     *  - 'forms': Tab-toggled form variants, each with their own fields
     */
    public function getGroupedForms(string $locale = 'en'): array
    {
        $fields = $this->formFields()->orderBy('sort_order')->get();

        if ($fields->isEmpty()) {
            return ['fields' => [], 'forms' => []];
        }

        $globalFields = [];
        $formGroups = [];

        foreach ($fields as $field) {
            $formKey = $field->ui_meta['form_key'] ?? null;

            $fieldData = [
                'key' => $field->field_key,
                'label' => $field->{"label_{$locale}"},
                'type' => $field->field_type,
                'required' => $field->is_required,
                'placeholder' => $field->{"placeholder_{$locale}"} ?? '',
                'options' => $field->ui_meta['options'] ?? [],
                'rules' => $field->validation_rules ?? [],
            ];

            if ($formKey === null) {
                $globalFields[] = $fieldData;
            } else {
                if (!isset($formGroups[$formKey])) {
                    $formGroups[$formKey] = [
                        'key' => $formKey,
                        'label' => $field->ui_meta["form_label_{$locale}"] ?? $field->ui_meta['form_label_en'] ?? ucfirst(str_replace('-', ' ', $formKey)),
                        'is_default' => $field->ui_meta['is_default_form'] ?? false,
                        'fields' => [],
                    ];
                }
                $formGroups[$formKey]['fields'][] = $fieldData;
            }
        }

        $result = array_values($formGroups);

        // Ensure at least one form is marked as default
        if (!empty($result) && !collect($result)->contains(fn ($f) => $f['is_default'])) {
            $result[0]['is_default'] = true;
        }

        return [
            'fields' => $globalFields,
            'forms' => $result,
        ];
    }
}
