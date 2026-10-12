<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'product_type',
        'brand',
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'cost_price',
        'tax_class',
        'tax_rate',
        'track_inventory',
        'allow_backorder',
        'min_order_quantity',
        'max_order_quantity',
        'requires_shipping',
        'weight',
        'length',
        'width',
        'height',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured',
        'status',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',

            'track_inventory' => 'boolean',
            'allow_backorder' => 'boolean',
            'requires_shipping' => 'boolean',

            'min_order_quantity' => 'integer',
            'max_order_quantity' => 'integer',

            'weight' => 'decimal:3',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',

            'featured' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',

            'published_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_product'
        )
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Collections
    |--------------------------------------------------------------------------
    */

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            Collection::class,
            'collection_product'
        )
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Product Media
    |--------------------------------------------------------------------------
    */

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)
            ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Product Variants
    |--------------------------------------------------------------------------
    */

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('sort_order');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');
    }
}
