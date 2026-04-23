<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Enums\OrderStatus;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use App\Traits\NotifyAdmins;

class Order extends Model
{
    use HasFactory, NotifyAdmins;

    public function toAdminNotification(): array
    {
        return [
            'type' => 'New Order',
            'message' => "Order #{$this->order_number} for {$this->product?->name_en} (\${$this->total_price})",
            'link' => route('admin.orders.show', $this),
            'icon' => 'order',
        ];
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'product_id',
        'package_id',
        'quantity',
        'unit_price',
        'total_price',
        'cost_price',
        'profit',
        'status',
        'delivery_type',
        'fulfillment_data',
        'delivery_notes',
        'refund_notes',
        'fulfilled_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'profit' => 'decimal:2',
            'status' => OrderStatus::class,
            'fulfillment_data' => AsEncryptedArrayObject::class,
            'fulfilled_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function getWaitTimeAttribute(): string
    {
        $diff = $this->created_at->diff(now());
        if ($diff->d > 0) return $diff->d . 'd ' . $diff->h . 'h';
        if ($diff->h > 0) return $diff->h . 'h ' . $diff->i . 'm';
        return $diff->i . 'm';
    }

    public function isTypeKey(): bool
    {
        return ($this->product?->resolvedProductType() === ProductType::FixedPackage);
    }

    public function isTypeAccount(): bool
    {
        return ($this->product?->resolvedProductType() === ProductType::AccountTopup);
    }

    public function isTypeTopup(): bool
    {
        return ($this->product?->resolvedProductType() === ProductType::CustomQuantity);
    }

    public function getUserInput(): array
    {
        return (array) ($this->fulfillment_data['user_input'] ?? []);
    }

    public function getFulfillmentDetails(): array
    {
        $details = (array) ($this->fulfillment_data['fulfillment'] ?? []);
        
        // Normalize links in admin_note if present
        if (isset($details['admin_note'])) {
            $details['admin_note'] = $this->normalizeHtmlLinks($details['admin_note']);
        }

        // Normalize links in nested data (keys, account_details, etc)
        if (isset($details['data']) && is_array($details['data'])) {
            foreach ($details['data'] as $key => $value) {
                if (is_string($value)) {
                    $details['data'][$key] = $this->normalizeHtmlLinks($value);
                }
            }
        }

        return $details;
    }

    /**
     * Ensures links in HTML have protocols and open in new tabs.
     */
    private function normalizeHtmlLinks(string $html): string
    {
        // 1. Fix links missing protocol (e.g. google.com -> https://google.com)
        // Look for href="something" where something doesn't start with protocol, /, #, or mailto:
        $html = preg_replace_callback(
            '/href=["\'](?!https?:\/\/|mailto:|tel:|\/|#)([^"\']+)["\']/i',
            function ($matches) {
                $link = $matches[1];
                // Handle email addresses
                if (filter_var($link, FILTER_VALIDATE_EMAIL)) {
                    return 'href="mailto:' . $link . '"';
                }
                // Handle standard domains
                return 'href="https://' . $link . '"';
            },
            $html
        );

        // 2. Add target="_blank" to external links for safety and UX
        $html = preg_replace_callback(
            '/<a\s+([^>]*?)href=["\'](https?:\/\/[^"\']+)["\']([^>]*?)>/i',
            function ($matches) {
                $before = $matches[1];
                $href = $matches[2];
                $after = $matches[3];
                
                // Only add if not already present
                if (!str_contains($before, 'target=') && !str_contains($after, 'target=')) {
                    return '<a ' . $before . ' href="' . $href . '"' . $after . ' target="_blank" rel="noopener noreferrer">';
                }
                return $matches[0];
            },
            $html
        );

        return $html;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ProductPackage::class, 'package_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class)->where('type', 'feedback');
    }

    public function report(): HasOne
    {
        return $this->hasOne(Feedback::class)->where('type', 'report')->latestOfMany();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Feedback::class)->where('type', 'report');
    }

    /**
     * Scope a query to only include pending or processing orders.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [OrderStatus::Pending, OrderStatus::Processing, OrderStatus::Reported]);
    }
}
