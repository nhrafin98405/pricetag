<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',

        'product_name',
        'product_sku',
        'variant_name',
        'variant_sku',

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
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
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)
            ->orderByDesc('created_at');
    }
}
