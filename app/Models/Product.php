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
}
