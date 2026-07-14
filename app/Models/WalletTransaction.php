<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description_ar',
        'description_en',
        'processed_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => \App\Enums\WalletTransactionType::class,
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return (float) $this->amount > 0;
    }

    public function signedAmountLabel(): string
    {
        $amount = abs((float) $this->amount);

        return sprintf('%s$%s', $this->isCredit() ? '+' : '-', number_format($amount, 2));
    }

    public function typeLabel(): string
    {
        $type = $this->type instanceof \BackedEnum ? $this->type->value : (string) $this->type;

        return ucwords(str_replace('_', ' ', $type));
    }
}
