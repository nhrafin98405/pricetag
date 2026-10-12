<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PaymentProviderMethod extends Pivot
{
    protected $table = 'payment_provider_method';

    public $incrementing = true;

    protected $fillable = [
        'payment_provider_id',
        'payment_method_id',
        'status',
        'is_default',
        'priority',
        'settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_default' => 'boolean',
            'priority' => 'integer',
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }
}