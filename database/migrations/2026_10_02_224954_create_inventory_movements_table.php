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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            // Stock record affected by this movement
            $table->foreignId('inventory_stock_id')
                ->constrained('inventory_stocks')
                ->restrictOnDelete();

            // Product reference for easier reporting
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Optional variant reference
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->restrictOnDelete();

            // Warehouse/location where movement occurred
            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('inventory_locations')
                ->restrictOnDelete();

            /*
             * Movement type:
             *
             * purchase       = stock received
             * sale           = stock sold
             * return         = customer/supplier return
             * adjustment     = manual stock correction
             * damage         = damaged/lost stock
             * transfer_in    = transferred into this location
             * transfer_out   = transferred from this location
             * opening        = initial stock
             */
            $table->string('type', 30)->index();

            /*
             * Signed quantity:
             *
             * Positive = stock added
             * Negative = stock removed
             */
            $table->integer('quantity');

            // Stock state before this movement
            $table->unsignedInteger('quantity_before')->default(0);

            // Stock state after this movement
            $table->unsignedInteger('quantity_after')->default(0);

            // Optional reference to external/business transaction
            $table->string('reference_type', 100)->nullable();
            $table->string('reference_id', 100)->nullable();

            // Human-readable reason/description
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();

            // User who performed/created the movement
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Movement timestamp
            $table->timestamp('occurred_at')->useCurrent();

            $table->timestamps();

            // Reporting indexes
            $table->index(
                ['inventory_stock_id', 'occurred_at'],
                'inventory_movements_stock_occurred_index'
            );

            $table->index(
                ['product_id', 'occurred_at'],
                'inventory_movements_product_occurred_index'
            );

            $table->index(
                ['warehouse_id', 'location_id', 'occurred_at'],
                'inventory_movements_location_occurred_index'
            );

            $table->index(
                ['type', 'occurred_at'],
                'inventory_movements_type_occurred_index'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'inventory_movements_reference_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};