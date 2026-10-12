<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_number',
        'provider',
        'method',
        'status',
        'currency',
        'amount',
        'transaction_id',
        'provider_transaction_id',
        'authorization_code',
        'provider_response',
        'metadata',
        'failure_code',
        'failure_message',
        'initiated_at',
        'authorized_at',
        'paid_at',
        'failed_at',
        'cancelled_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'provider_response' => 'array',
            'metadata' => 'array',

            'initiated_at' => 'datetime',
            'authorized_at' => 'datetime',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }
    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class)
            ->orderByDesc('created_at');
    }
}
