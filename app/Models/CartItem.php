<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'unit_price',
        'compare_at_price',
        'quantity',
        'discount_total',
        'tax_total',
        'line_total',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'quantity' => 'integer',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'line_total' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getFinalUnitPriceAttribute(): float
    {
        return (float) $this->unit_price;
    }

    public function getCalculatedLineTotalAttribute(): float
    {
        return max(
            0,
            ($this->quantity * (float) $this->unit_price)
            - (float) $this->discount_total
            + (float) $this->tax_total
        );
    }
}