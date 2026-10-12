<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->restrictOnDelete();

            /*
             * Product/variant snapshots.
             * These preserve what the customer added to the cart
             * even if the product name changes later.
             */
            $table->string('product_name');
            $table->string('variant_name')->nullable();

            /*
             * Price snapshots.
             */
            $table->decimal('unit_price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();

            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            /*
             * Future extensibility:
             * custom options, personalization, selected attributes, etc.
             */
            $table->json('metadata')->nullable();

            $table->timestamps();

            /*
             * Same product + same variant should not be duplicated
             * inside one cart.
             *
             * Because product_variant_id is nullable, MySQL permits
             * multiple NULL values. Application/service logic will
             * handle product-only cart item uniqueness.
             */
            $table->unique(
                ['cart_id', 'product_id', 'product_variant_id'],
                'cart_items_cart_product_variant_unique'
            );

            $table->index(
                ['cart_id', 'created_at'],
                'cart_items_cart_created_index'
            );

            $table->index(
                ['product_id', 'product_variant_id'],
                'cart_items_product_variant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};