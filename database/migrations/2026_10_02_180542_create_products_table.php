<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */
            $table->string('name');
            $table->string('slug')->unique();

            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->index();

            $table->string('product_type', 30)
                ->default('simple')
                ->index();

            $table->string('brand')->nullable();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('price', 12, 2)
                ->default(0);

            $table->decimal('compare_at_price', 12, 2)
                ->nullable();

            $table->decimal('cost_price', 12, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */
            $table->string('tax_class')->nullable();

            $table->decimal('tax_rate', 5, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */
            $table->boolean('track_inventory')
                ->default(true);

            $table->boolean('allow_backorder')
                ->default(false);

            $table->unsignedInteger('min_order_quantity')
                ->default(1);

            $table->unsignedInteger('max_order_quantity')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */
            $table->boolean('requires_shipping')
                ->default(true);

            $table->decimal('weight', 10, 3)
                ->nullable();

            $table->decimal('length', 10, 2)
                ->nullable();

            $table->decimal('width', 10, 2)
                ->nullable();

            $table->decimal('height', 10, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */
            $table->string('meta_title')->nullable();

            $table->text('meta_description')->nullable();

            $table->text('meta_keywords')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Display & Publishing
            |--------------------------------------------------------------------------
            */
            $table->boolean('featured')
                ->default(false)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamp('published_at')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Timestamps & Soft Delete
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Composite Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['status', 'featured', 'sort_order'],
                'products_status_featured_sort_index'
            );

            $table->index(
                ['status', 'published_at'],
                'products_status_published_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};