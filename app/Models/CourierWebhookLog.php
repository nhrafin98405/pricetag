<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_provider_id',
        'shipment_id',

        'event_id',
        'event_type',
        'provider_event_type',

        'status',
        'http_method',
        'signature',
        'signature_verified',

        'headers',
        'payload',
        'normalized_data',

        'processing_error',
        'attempts',

        'received_at',
        'processed_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'signature_verified' => 'boolean',

            'headers' => 'array',
            'payload' => 'array',
            'normalized_data' => 'array',

            'attempts' => 'integer',

            'received_at' => 'datetime',
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function courierProvider(): BelongsTo
    {
        return $this->belongsTo(CourierProvider::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getIsReceivedAttribute(): bool
    {
        return $this->status === 'received';
    }

    public function getIsProcessedAttribute(): bool
    {
        return $this->status === 'processed';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsSignatureVerifiedAttribute(): bool
    {
        return $this->signature_verified === true;
    }
}