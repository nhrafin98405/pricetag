<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'reorder_level',
        'reorder_quantity',
        'status',
        'last_counted_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'reorder_level' => 'integer',
            'reorder_quantity' => 'integer',
            'status' => 'boolean',
            'last_counted_at' => 'datetime',
        ];
    }

    /**
     * Warehouse where this stock is stored.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Specific inventory location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class);
    }

    /**
     * Product associated with this stock.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Optional product variant.
     *
     * NULL means the stock belongs directly to the product.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Available stock.
     *
     * Available = Physical Quantity - Reserved Quantity
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(
            0,
            $this->quantity - $this->reserved_quantity
        );
    }

    /**
     * Determine whether the stock is below reorder level.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->available_quantity <= $this->reorder_level;
    }

    /**
     * Determine whether the product can fulfill the requested quantity.
     */
    public function canFulfill(int $quantity): bool
    {
        return $quantity > 0
            && $this->available_quantity >= $quantity;
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)
            ->orderByDesc('occurred_at');
    }
}
