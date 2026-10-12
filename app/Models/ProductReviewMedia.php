<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_review_id',

        'type',
        'disk',
        'file_path',
        'thumbnail',

        'title',
        'alt_text',

        'mime_type',
        'file_size',
        'duration',

        'sort_order',
        'is_primary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'duration' => 'decimal:2',

            'sort_order' => 'integer',

            'is_primary' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function productReview(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === true;
    }
}