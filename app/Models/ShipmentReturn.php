<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ShipmentReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'order_id',
        'courier_provider_id',
        'courier_service_id',

        'return_number',
        'tracking_number',
        'provider_return_id',

        'status',
        'reason',
        'notes',

        'recipient_name',
        'recipient_phone',
        'address_line_1',
        'address_line_2',
        'area',
        'city',
        'state',
        'postal_code',
        'country',

        'shipping_cost',

        'requested_at',
        'picked_up_at',
        'in_transit_at',
        'received_at',
        'cancelled_at',

        'provider_response',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',

            'requested_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'in_transit_at' => 'datetime',
            'received_at' => 'datetime',
            'cancelled_at' => 'datetime',

            'provider_response' => 'array',
            'metadata' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function courierProvider(): BelongsTo
    {
        return $this->belongsTo(CourierProvider::class);
    }

    public function courierService(): BelongsTo
    {
        return $this->belongsTo(CourierService::class);
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsPickedUpAttribute(): bool
    {
        return $this->status === 'picked_up';
    }

    public function getIsInTransitAttribute(): bool
    {
        return $this->status === 'in_transit';
    }

    public function getIsReceivedAttribute(): bool
    {
        return $this->status === 'received';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }
}