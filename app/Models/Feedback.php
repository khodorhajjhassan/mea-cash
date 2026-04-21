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
            'type' => $this->type === 'report' ? 'Order Report' : 'New Feedback',
            'message' => $this->type === 'report'
                ? "Order #{$this->order?->order_number} was reported by {$this->user?->name}"
                : "Order #{$this->order?->order_number} rated {$this->rating}/5 by {$this->user?->name}",
            'link' => route('admin.feedback.show', $this),
            'icon' => $this->type === 'report' ? 'support_agent' : 'star',
        ];
    }

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'rating',
        'comment',
        'issue_type',
        'status',
        'admin_response',
        'resolved_at',
        'show_on_homepage',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'show_on_homepage' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
