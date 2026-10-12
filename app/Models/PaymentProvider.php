<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'type',
        'driver',
        'description',
        'is_test_mode',
        'status',
        'sort_order',
        'credentials',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_test_mode' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',

            // Sensitive provider credentials
            'credentials' => 'encrypted:array',

            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function methods(): BelongsToMany
    {
        return $this->belongsToMany(
            PaymentMethod::class,
            'payment_provider_method',
            'payment_provider_id',
            'payment_method_id'
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

    public function activeMethods(): BelongsToMany
    {
        return $this->methods()
            ->wherePivot('status', true);
    }

    public function defaultMethods(): BelongsToMany
    {
        return $this->methods()
            ->wherePivot('is_default', true)
            ->wherePivot('status', true);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }

    public function getIsProductionAttribute(): bool
    {
        return $this->is_test_mode === false;
    }
}
