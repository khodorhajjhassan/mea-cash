<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'code_id',
        'delivered_value',
        'type',
        'revealed_at',
    ];

    protected function casts(): array
    {
        return [
            'delivered_value' => 'encrypted',
            'revealed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(ProductCode::class, 'code_id');
    }
}
