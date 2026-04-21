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
            'message' => "{$this->user?->name} requested a top-up of \${$this->amount_requested} via " . strtoupper($this->payment_method),
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
            'amount_requested' => 'decimal:2',
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

    public function getReceiptUrl(): ?string
    {
        if (!$this->receipt_image_path) {
            return null;
        }

        try {
            if (\Illuminate\Support\Facades\Storage::disk('private')->exists($this->receipt_image_path)) {
                return \Illuminate\Support\Facades\Storage::disk('private')->temporaryUrl(
                    $this->receipt_image_path,
                    now()->addMinutes(30)
                );
            }
        } catch (\Throwable $e) {
            // Fallback to public if private fails (backward compatibility)
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->receipt_image_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->receipt_image_path);
        }

        return null;
    }
}
