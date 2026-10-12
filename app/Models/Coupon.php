<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',

        'discount_type',
        'discount_value',
        'maximum_discount_amount',
        'minimum_order_amount',
        'minimum_quantity',

        'usage_limit',
        'usage_limit_per_customer',
        'usage_count',

        'is_first_order_only',
        'is_free_shipping',
        'individual_use',
        'exclude_sale_items',

        'starts_at',
        'expires_at',

        'status',
        'sort_order',
        'created_by',

        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'maximum_discount_amount' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'minimum_quantity' => 'decimal:2',

            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'usage_count' => 'integer',

            'is_first_order_only' => 'boolean',
            'is_free_shipping' => 'boolean',
            'individual_use' => 'boolean',
            'exclude_sale_items' => 'boolean',

            'starts_at' => 'datetime',
            'expires_at' => 'datetime',

            'status' => 'boolean',
            'sort_order' => 'integer',

            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function getIsStartedAttribute(): bool
    {
        return $this->starts_at === null
            || $this->starts_at->isPast();
    }

    public function getIsCurrentlyValidAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->is_started) {
            return false;
        }

        if ($this->is_expired) {
            return false;
        }

        return true;
    }

    public function getIsUsageLimitReachedAttribute(): bool
    {
        return $this->usage_limit !== null
            && $this->usage_count >= $this->usage_limit;
    }

    public function canBeUsed(): bool
    {
        return $this->is_currently_valid
            && ! $this->is_usage_limit_reached;
    }
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class)
            ->orderByDesc('created_at');
    }
    
}
