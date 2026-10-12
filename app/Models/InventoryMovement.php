<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_stock_id',
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'location_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'reason',
        'notes',
        'created_by',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'quantity_before' => 'integer',
            'quantity_after' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    /**
     * Inventory stock affected by this movement.
     */
    public function inventoryStock(): BelongsTo
    {
        return $this->belongsTo(InventoryStock::class);
    }

    /**
     * Product associated with this movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Optional product variant.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Warehouse where the movement occurred.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Location where the movement occurred.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class);
    }

    /**
     * User who created the movement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Determine whether this movement adds stock.
     */
    public function getIsInboundAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Determine whether this movement removes stock.
     */
    public function getIsOutboundAttribute(): bool
    {
        return $this->quantity < 0;
    }
}