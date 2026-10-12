<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_provider_id',
        'courier_service_id',
        'warehouse_id',

        'shipment_number',
        'tracking_number',
        'provider_shipment_id',

        'status',
        'fulfillment_status',
        'delivery_status',
        'type',

        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'address_line_1',
        'address_line_2',
        'area',
        'city',
        'state',
        'postal_code',
        'country',

        'package_count',
        'weight',
        'length',
        'width',
        'height',
        'package_type',
        'package_description',

        'shipping_cost',
        'cod_amount',
        'cod_collected_amount',
        'insurance_amount',

        'scheduled_at',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'cancelled_at',

        'provider_response',
        'metadata',

        'customer_note',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'package_count' => 'integer',

            'weight' => 'decimal:3',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',

            'shipping_cost' => 'decimal:2',
            'cod_amount' => 'decimal:2',
            'cod_collected_amount' => 'decimal:2',
            'insurance_amount' => 'decimal:2',

            'scheduled_at' => 'datetime',
            'shipped_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',

            'provider_response' => 'array',
            'metadata' => 'array',
        ];
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

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class)
            ->orderByDesc('occurred_at');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class)
            ->orderByDesc('created_at');
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsShippedAttribute(): bool
    {
        return $this->shipped_at !== null;
    }
    public function returns(): HasMany
    {
        return $this->hasMany(ShipmentReturn::class)
            ->orderByDesc('created_at');
    }
    public function webhookLogs(): HasMany
    {
        return $this->hasMany(CourierWebhookLog::class)
            ->orderByDesc('created_at');
    }
}
