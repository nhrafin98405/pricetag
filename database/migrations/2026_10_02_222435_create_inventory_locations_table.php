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
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 50);

            $table->string('type', 30)
                ->default('storage')
                ->index();

            $table->text('description')->nullable();

            $table->string('aisle', 50)->nullable();
            $table->string('rack', 50)->nullable();
            $table->string('shelf', 50)->nullable();
            $table->string('bin', 50)->nullable();

            $table->boolean('status')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['warehouse_id', 'code'],
                'inventory_locations_warehouse_code_unique'
            );

            $table->index(
                ['warehouse_id', 'status', 'sort_order'],
                'inventory_locations_warehouse_status_sort_index'
            );

            $table->index(
                ['warehouse_id', 'status', 'is_default'],
                'inventory_locations_warehouse_status_default_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};