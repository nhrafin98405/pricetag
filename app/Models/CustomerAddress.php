<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'company_name',
        'phone',
        'alternate_phone',
        'address_line_1',
        'address_line_2',
        'area',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default_shipping',
        'is_default_billing',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default_shipping' => 'boolean',
            'is_default_billing' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->area,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ])
            ->filter()
            ->implode(', ');
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            collect([
                $this->first_name,
                $this->last_name,
            ])->filter()->implode(' ')
        );
    }
}