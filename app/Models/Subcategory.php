<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'product_type_id',
        'delivery_type',
        'delivery_time_minutes',
        'name_ar',
        'name_en',
        'slug',
        'image',
        'is_active',
        'is_featured',
        'description_ar',
        'description_en',
        'sort_order',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_image',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'delivery_time_minutes' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productTypeDefinition(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
