<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'field_key',
        'label_ar',
        'label_en',
        'field_type',
        'placeholder_ar',
        'placeholder_en',
        'is_required',
        'sort_order',
        'validation_rules',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'validation_rules' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
