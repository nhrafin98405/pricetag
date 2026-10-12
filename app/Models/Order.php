<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'fulfillment_status',
        'currency',

        'customer_name',
        'customer_email',
        'customer_phone',

        'billing_first_name',
        'billing_last_name',
        'billing_company_name',
        'billing_phone',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_area',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',

        'shipping_first_name',
        'shipping_last_name',
        'shipping_company_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_area',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',

        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',

        'coupon_code',
        'customer_note',
        'admin_note',
        'source',

        'placed_at',
        'paid_at',
        'cancelled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'grand_total' => 'decimal:2',

            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getIsGuestAttribute(): bool
    {
        return is_null($this->user_id);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->sum('quantity');
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class)
            ->orderByDesc('created_at');
    }
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)
            ->orderByDesc('created_at');
    }
}
