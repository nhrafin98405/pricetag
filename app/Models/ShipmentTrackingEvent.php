<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentTrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',

        'status',
        'title',
        'description',

        'location',
        'city',
        'state',
        'country',

        'event_code',
        'provider_status',

        'occurred_at',

        'is_customer_visible',
        'is_current',

        'provider_response',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',

            'is_customer_visible' => 'boolean',
            'is_current' => 'boolean',

            'provider_response' => 'array',
            'metadata' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getIsCustomerVisibleAttribute(): bool
    {
        return $this->is_customer_visible === true;
    }

    public function getIsCurrentAttribute(): bool
    {
        return $this->is_current === true;
    }
}