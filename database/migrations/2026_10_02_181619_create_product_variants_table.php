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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Product Relationship
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Variant Identification
            |--------------------------------------------------------------------------
            */
            $table->string('name');

            $table->string('sku')
                ->unique();

            $table->string('barcode')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('price', 12, 2)
                ->nullable();

            $table->decimal('compare_at_price', 12, 2)
                ->nullable();

            $table->decimal('cost_price', 12, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */
            $table->decimal('weight', 10, 3)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Variant Status & Display
            |--------------------------------------------------------------------------
            */
            $table->boolean('status')
                ->default(true)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Composite Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['product_id', 'status', 'sort_order'],
                'product_variants_product_status_sort_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};