<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'type',
        'driver',
        'description',
        'website',
        'support_phone',
        'support_email',
        'is_test_mode',
        'status',
        'is_default',
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
            'is_default' => 'boolean',
            'sort_order' => 'integer',
            'credentials' => 'encrypted:array',
            'settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }

    public function getIsTestModeAttribute(): bool
    {
        return $this->is_test_mode === true;
    }
    public function services(): HasMany
    {
        return $this->hasMany(CourierService::class)
            ->orderBy('sort_order');
    }
    public function webhookLogs(): HasMany
    {
        return $this->hasMany(CourierWebhookLog::class)
            ->orderByDesc('created_at');
    }
}
