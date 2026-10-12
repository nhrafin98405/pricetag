<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            // Product snapshot
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('variant_name')->nullable();
            $table->string('variant_sku')->nullable();

            // Pricing snapshot
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            // Flexible snapshot / future extensibility
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['order_id', 'created_at'],
                'order_items_order_created_index'
            );

            $table->index(
                ['product_id', 'product_variant_id'],
                'order_items_product_variant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};