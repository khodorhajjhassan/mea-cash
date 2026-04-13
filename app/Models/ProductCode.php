<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'package_id',
        'code',
        'notes',
        'status',
        'order_id',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'code' => 'encrypted',
            'used_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ProductPackage::class, 'package_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
