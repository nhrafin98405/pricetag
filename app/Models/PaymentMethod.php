<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'type',
        'description',
        'icon',
        'status',
        'sort_order',
        'supports_refund',
        'supports_partial_refund',
        'supports_webhook',
        'supports_recurring',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer',
            'supports_refund' => 'boolean',
            'supports_partial_refund' => 'boolean',
            'supports_webhook' => 'boolean',
            'supports_recurring' => 'boolean',
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(
            PaymentProvider::class,
            'payment_provider_method',
            'payment_method_id',
            'payment_provider_id'
        )
            ->using(PaymentProviderMethod::class)
            ->withPivot([
                'status',
                'is_default',
                'priority',
                'settings',
                'metadata',
            ])
            ->withTimestamps()
            ->orderByPivot('priority');
    }

    public function activeProviders(): BelongsToMany
    {
        return $this->providers()
            ->wherePivot('status', true);
    }

    public function defaultProviders(): BelongsToMany
    {
        return $this->providers()
            ->wherePivot('is_default', true)
            ->wherePivot('status', true);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }
}
