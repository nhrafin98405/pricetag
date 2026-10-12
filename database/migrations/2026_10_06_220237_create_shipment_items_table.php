<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->string('product_name');
            $table->string('product_sku')
                ->nullable();

            $table->string('variant_name')
                ->nullable();

            $table->string('variant_sku')
                ->nullable();

            $table->unsignedInteger('quantity')
                ->default(1);

            $table->decimal('unit_price', 12, 2)
                ->default(0);

            $table->decimal('line_total', 12, 2)
                ->default(0);

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->unique(
                ['shipment_id', 'order_item_id'],
                'shipment_items_shipment_order_item_unique'
            );

            $table->index(
                ['shipment_id', 'created_at'],
                'shipment_items_shipment_created_index'
            );

            $table->index(
                ['order_item_id'],
                'shipment_items_order_item_index'
            );

            $table->index(
                ['product_id', 'product_variant_id'],
                'shipment_items_product_variant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};