<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('code', 50)->unique();

            // mfs / card / gateway / cod / bank_transfer / manual
            $table->string('type', 30)->index();

            $table->string('description')->nullable();

            // Customer-facing icon/logo
            $table->string('icon')->nullable();

            // Whether customers can currently select this method
            $table->boolean('status')->default(true)->index();

            // Admin display order
            $table->unsignedInteger('sort_order')->default(0);

            // Capability flags
            $table->boolean('supports_refund')->default(false);
            $table->boolean('supports_partial_refund')->default(false);
            $table->boolean('supports_webhook')->default(false);
            $table->boolean('supports_recurring')->default(false);

            // Method-specific non-sensitive configuration
            $table->json('settings')->nullable();

            // Additional metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['status', 'sort_order'],
                'payment_methods_status_sort_index'
            );

            $table->index(
                ['type', 'status'],
                'payment_methods_type_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};