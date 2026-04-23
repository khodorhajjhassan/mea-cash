<?php

namespace App\Models;

use App\Enums\ProductType as ProductTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'supplier_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'slug',
        'delivery_type',
        'delivery_time_minutes',
        'cost_price',
        'selling_price',
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
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
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

    public function packages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductPackage::class);
    }

    public function codes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductCode::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function resolvedTemplate(): ?ProductType
    {
        if ($this->relationLoaded('subcategory')) {
            return $this->subcategory?->productTypeDefinition;
        }

        if (! $this->subcategory_id) {
            return null;
        }

        $subcategory = $this->subcategory()->with('productTypeDefinition:id,name,key,schema')->first();

        return $subcategory?->productTypeDefinition;
    }

    public function resolvedProductType(): ProductTypeEnum
    {
        return ProductTypeEnum::fromTemplate($this->resolvedTemplate());
    }

    public function resolvedProductTypeLabel(): string
    {
        return $this->resolvedProductType()->label();
    }

    public function getGroupedForms(string $locale = 'en'): array
    {
        $template = $this->resolvedTemplate();
        $schema = is_array($template?->schema) ? $template->schema : [];
        $globalFields = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];
        $forms = is_array($schema['forms'] ?? null) ? $schema['forms'] : [];

        if ($globalFields === [] && $forms === []) {
            return ['fields' => [], 'forms' => []];
        }

        $normalizedGlobalFields = collect($globalFields)
            ->filter(fn ($field) => is_array($field))
            ->values()
            ->map(fn (array $field, int $index) => $this->normalizeTemplateField($field, $locale, $index))
            ->all();

        $normalizedForms = collect($forms)
            ->filter(fn ($form) => is_array($form))
            ->values()
            ->map(function (array $form, int $index) use ($locale) {
                $key = Str::slug((string) ($form['key'] ?? 'form_'.($index + 1)));
                if ($key === '') {
                    $key = 'form_'.($index + 1);
                }

                return [
                    'key' => $key,
                    'label' => (string) ($form["label_{$locale}"] ?? $form['label_en'] ?? $form['label'] ?? Str::headline($key)),
                    'is_default' => (bool) ($form['is_default'] ?? false),
                    'fields' => collect($form['fields'] ?? [])
                        ->filter(fn ($field) => is_array($field))
                        ->values()
                        ->map(fn (array $field, int $fieldIndex) => $this->normalizeTemplateField($field, $locale, $fieldIndex))
                        ->all(),
                ];
            })
            ->all();

        if ($normalizedForms !== [] && ! collect($normalizedForms)->contains(fn ($form) => $form['is_default'])) {
            $normalizedForms[0]['is_default'] = true;
        }

        return [
            'fields' => $normalizedGlobalFields,
            'forms' => $normalizedForms,
        ];
    }

    public function resolvedFieldDefinitions(string $locale = 'en', ?string $selectedForm = null): array
    {
        $grouped = $this->getGroupedForms($locale);
        $activeForm = collect($grouped['forms'] ?? [])->firstWhere('key', $selectedForm)
            ?? collect($grouped['forms'] ?? [])->firstWhere('is_default', true)
            ?? ($grouped['forms'][0] ?? null);

        return [
            ...($grouped['fields'] ?? []),
            ...($activeForm['fields'] ?? []),
        ];
    }

    public function resolvedFieldLabel(string $fieldKey, string $locale = 'en', ?string $selectedForm = null): ?string
    {
        return collect($this->resolvedFieldDefinitions($locale, $selectedForm))
            ->firstWhere('key', $fieldKey)['label'] ?? null;
    }

    private function normalizeTemplateField(array $field, string $locale, int $index): array
    {
        $fieldKey = Str::slug((string) ($field['key'] ?? ''));
        if ($fieldKey === '') {
            $fieldKey = 'field_'.($index + 1);
        }

        $type = (string) ($field['type'] ?? 'text');
        if (! in_array($type, ['text', 'email', 'password', 'number', 'select'], true)) {
            $type = 'text';
        }

        return [
            'key' => $fieldKey,
            'label' => (string) ($field["label_{$locale}"] ?? $field['label_en'] ?? $field['label'] ?? Str::headline($fieldKey)),
            'type' => $type,
            'required' => (bool) ($field['required'] ?? false),
            'placeholder' => (string) ($field["placeholder_{$locale}"] ?? $field['placeholder_en'] ?? $field['placeholder'] ?? ''),
            'options' => is_array($field['options'] ?? null) ? $field['options'] : [],
            'min' => $field['min'] ?? null,
            'max' => $field['max'] ?? null,
            'rules' => is_array($field['rules'] ?? null) ? array_values($field['rules']) : [],
        ];
    }
}
