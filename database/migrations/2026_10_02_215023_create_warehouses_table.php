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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code', 50)->unique();

            $table->text('description')->nullable();

            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('country', 100)->default('Bangladesh');

            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            $table->string('manager_name')->nullable();

            $table->boolean('status')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['status', 'sort_order'],
                'warehouses_status_sort_index'
            );

            $table->index(
                ['status', 'is_default'],
                'warehouses_status_default_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};