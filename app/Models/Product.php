<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

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
        'weight_value',
        'weight_unit',
        'price_value',
        'currency',
        'price_display',
        'unit_price_display',
        'stock',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_value' => 'decimal:2',
            'price_value' => 'decimal:2',
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
}
