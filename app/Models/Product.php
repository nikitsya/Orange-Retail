<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    public $timestamps = false;

    /**
     * @var list<string>
     */
    public const UNIT_TYPES = [
        'each',
        'pack',
        'volume',
        'weight',
    ];

    /**
     * @var list<string>
     */
    public const CATEGORIES = [
        'Fresh Food',
        'Drinks',
        'Food Cupboard',
        'Treats & Snacks',
        'Household',
        'Pets',
        'Health & Beauty',
        'Baby & Toddler',
        'Home & Furniture',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'brand',
        'category',
        'subcategory',
        'image_url',
        'unit_type',
        'pack_size',
        'price_value',
        'unit_price_display',
        'stock',
        'minimum_stock_level',
        'is_active',
        'last_restocked_at',
        'next_delivery_due_at',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_products')
            ->withTimestamps();
    }

    /**
     * @return list<string>
     */
    public static function unitTypes(): array
    {
        return self::UNIT_TYPES;
    }

    /**
     * @return list<string>
     */
    public static function categories(): array
    {
        return self::CATEGORIES;
    }

    public function scopeAtOrBelowMinimumStock($query)
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock_level');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_value' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock_level' => 'integer',
            'is_active' => 'boolean',
            'last_restocked_at' => 'datetime',
            'next_delivery_due_at' => 'datetime',
        ];
    }

    public function getSummaryTextAttribute(): string
    {
        $parts = collect([
            $this->name,
            $this->brand,
            $this->category,
            $this->subcategory,
            $this->pack_size ?: $this->unit_type,
        ])
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values();

        return $parts->isEmpty()
            ? 'Product information is available in the saved fields.'
            : $parts->implode(', ') . '.';
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if ($this->price_value === null) {
            return null;
        }

        return '€' . number_format((float) $this->price_value, 2);
    }
}
