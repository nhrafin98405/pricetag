<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasFactory;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
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

    /*
    |--------------------------------------------------------------------------
    | Product
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}