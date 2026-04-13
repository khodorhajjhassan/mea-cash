<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name_ar',
        'name_en',
        'amount',
        'cost_price',
        'selling_price',
        'image',
        'badge_text',
        'is_available',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function codes(): HasMany
    {
        return $this->hasMany(ProductCode::class, 'package_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'package_id');
    }
}
