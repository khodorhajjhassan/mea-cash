<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\NotifyAdmins;

class TopupRequest extends Model
{
    use HasFactory, NotifyAdmins;

    public function toAdminNotification(): array
    {
        return [
            'type' => 'Topup Request',
            'message' => "{$this->user?->name} requested a top-up of ${$this->amount_requested} via " . strtoupper($this->payment_method),
            'link' => route('admin.topups.show', $this),
            'icon' => 'wallet',
        ];
    }

    protected $fillable = [
        'user_id',
        'payment_method',
        'amount_requested',
        'receipt_image_path',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
