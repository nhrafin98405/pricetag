<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'from_status',
        'to_status',
        'reason',
        'notes',
        'source',
        'changed_by',
        'changed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getIsSystemChangeAttribute(): bool
    {
        return $this->source === 'system';
    }

    public function getIsCourierChangeAttribute(): bool
    {
        return $this->source === 'courier';
    }

    public function getIsAdminChangeAttribute(): bool
    {
        return $this->source === 'admin';
    }
}