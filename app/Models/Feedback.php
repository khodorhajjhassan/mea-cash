<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\NotifyAdmins;

class Feedback extends Model
{
    use HasFactory, NotifyAdmins;

    public function toAdminNotification(): array
    {
        return [
            'type' => 'New Feedback',
            'message' => "Order #{$this->order?->order_number} rated {$this->rating}/5 by {$this->user?->name}",
            'link' => route('admin.feedback.show', $this),
            'icon' => 'star',
        ];
    }

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
