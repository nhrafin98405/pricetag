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
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();

            // Stock location
            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('inventory_locations')
                ->restrictOnDelete();

            // Product reference
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Optional variant reference
            // NULL = simple product stock
            // Value = variant-specific stock
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->restrictOnDelete();

            // Physical stock currently held
            $table->unsignedInteger('quantity')->default(0);

            // Stock reserved for orders
            $table->unsignedInteger('reserved_quantity')->default(0);

            // Reorder configuration
            $table->unsignedInteger('reorder_level')->default(0);
            $table->unsignedInteger('reorder_quantity')->default(0);

            // Inventory record status
            $table->boolean('status')->default(true)->index();

            // Last physical stock count
            $table->timestamp('last_counted_at')->nullable();

            $table->timestamps();

            /*
             * A stock record belongs to one warehouse/location/product/variant.
             *
             * Note:
             * MySQL allows multiple NULL values in a UNIQUE index.
             * Therefore product-level stock (product_variant_id = NULL)
             * will also be protected at the application/service layer.
             */
            $table->unique(
                [
                    'warehouse_id',
                    'location_id',
                    'product_id',
                    'product_variant_id',
                ],
                'inventory_stocks_unique'
            );

            // Warehouse/location stock lookup
            $table->index(
                ['warehouse_id', 'location_id', 'status'],
                'inventory_stocks_location_status_index'
            );

            // Product stock lookup
            $table->index(
                ['product_id', 'product_variant_id', 'status'],
                'inventory_stocks_product_variant_status_index'
            );

            // Reorder/low-stock lookup
            $table->index(
                ['status', 'reorder_level'],
                'inventory_stocks_reorder_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};