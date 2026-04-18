<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSection extends Model
{
    use HasFactory;

    public const TYPE_CATEGORY_SHOWCASE = 'category_showcase';
    public const TYPE_BEST_SELLER = 'best_seller';
    public const TYPE_TOP_DEAL = 'top_deal';
    public const TYPE_FLASH_SALE = 'flash_sale';
    public const TYPE_NEW_ARRIVALS = 'new_arrivals';
    public const TYPE_MANUAL_PRODUCTS = 'manual_products';

    public const SOURCE_MANUAL_PRODUCTS = 'manual_products';
    public const SOURCE_CATEGORY = 'category';
    public const SOURCE_SUBCATEGORY = 'subcategory';
    public const SOURCE_SUBCATEGORIES = 'subcategories';
    public const SOURCE_PRODUCT_TYPE = 'product_type';
    public const SOURCE_BEST_SELLER = 'auto_best_seller';
    public const SOURCE_FEATURED = 'auto_featured';
    public const SOURCE_LATEST = 'auto_latest';

    protected $fillable = [
        'type',
        'source_type',
        'title_en',
        'title_ar',
        'subtitle_en',
        'subtitle_ar',
        'category_id',
        'subcategory_id',
        'subcategory_ids',
        'product_type_id',
        'product_ids',
        'settings',
        'limit',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
            'subcategory_ids' => 'array',
            'settings' => 'array',
            'limit' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_CATEGORY_SHOWCASE => 'Category Showcase',
            self::TYPE_BEST_SELLER => 'Best Seller',
            self::TYPE_TOP_DEAL => 'Top Deal',
            self::TYPE_FLASH_SALE => 'Flash Sale',
            self::TYPE_NEW_ARRIVALS => 'New Arrivals',
            self::TYPE_MANUAL_PRODUCTS => 'Manual Products',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_MANUAL_PRODUCTS => 'By Product',
            self::SOURCE_SUBCATEGORY => 'By Subcategory',
            self::SOURCE_SUBCATEGORIES => 'By Subcategories',
            self::SOURCE_BEST_SELLER => 'Auto Best Sellers',
            self::SOURCE_FEATURED => 'Auto Featured',
            self::SOURCE_LATEST => 'Auto Latest',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function productTypeDefinition(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
            ->where(function ($nested): void {
                $nested->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($nested): void {
                $nested->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
