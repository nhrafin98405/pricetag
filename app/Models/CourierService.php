<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierService extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_provider_id',
        'name',
        'slug',
        'code',
        'type',
        'description',
        'base_rate',
        'inside_city_rate',
        'outside_city_rate',
        'outside_dhaka_rate',
        'cod_fee',
        'cod_percentage',
        'estimated_delivery_days',
        'supports_cod',
        'supports_return',
        'supports_exchange',
        'supports_tracking',
        'status',
        'sort_order',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'inside_city_rate' => 'decimal:2',
            'outside_city_rate' => 'decimal:2',
            'outside_dhaka_rate' => 'decimal:2',
            'cod_fee' => 'decimal:2',
            'cod_percentage' => 'decimal:2',
            'estimated_delivery_days' => 'integer',
            'supports_cod' => 'boolean',
            'supports_return' => 'boolean',
            'supports_exchange' => 'boolean',
            'supports_tracking' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function courierProvider(): BelongsTo
    {
        return $this->belongsTo(CourierProvider::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }
}