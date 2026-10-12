<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'order_id',
        'order_item_id',

        'rating',
        'title',
        'body',

        'is_verified_purchase',
        'is_featured',
        'status',

        'admin_reply',
        'replied_by',
        'replied_at',

        'published_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',

            'is_verified_purchase' => 'boolean',
            'is_featured' => 'boolean',

            'replied_at' => 'datetime',
            'published_at' => 'datetime',

            'metadata' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'approved'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function getHasAdminReplyAttribute(): bool
    {
        return filled($this->admin_reply);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query
            ->where('is_featured', true)
            ->published();
    }
    public function media(): HasMany
    {
        return $this->hasMany(ProductReviewMedia::class)
            ->orderBy('sort_order')
            ->orderBy('created_at');
    }
}
