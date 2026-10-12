<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wishlist_id')
                ->constrained('wishlists')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->timestamps();

            /*
             * Prevent duplicate product/variant entries
             * inside the same wishlist.
             *
             * MySQL permits multiple NULL values for the nullable
             * variant column, so product-only duplicate protection
             * will also be enforced at the application/service layer.
             */
            $table->unique(
                ['wishlist_id', 'product_id', 'product_variant_id'],
                'wishlist_items_wishlist_product_variant_unique'
            );

            $table->index(
                ['wishlist_id', 'created_at'],
                'wishlist_items_wishlist_created_index'
            );

            $table->index(
                ['product_id', 'product_variant_id'],
                'wishlist_items_product_variant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};