<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_code',
        'first_name',
        'last_name',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'company_name',
        'notes',
        'avatar',
        'marketing_email_opt_in',
        'marketing_sms_opt_in',
        'status',
        'last_order_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'marketing_email_opt_in' => 'boolean',
            'marketing_sms_opt_in' => 'boolean',
            'status' => 'boolean',
            'last_order_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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