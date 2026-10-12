<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_provider_method', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_provider_id')
                ->constrained('payment_providers')
                ->cascadeOnDelete();

            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->cascadeOnDelete();

            // Whether this provider-method combination is active
            $table->boolean('status')->default(true)->index();

            // Preferred/default provider for this payment method
            $table->boolean('is_default')->default(false)->index();

            // Provider-specific priority
            $table->unsignedInteger('priority')->default(0);

            // Optional provider-method specific settings
            $table->json('settings')->nullable();

            // Additional metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(
                ['payment_provider_id', 'payment_method_id'],
                'payment_provider_method_unique'
            );

            $table->index(
                ['payment_method_id', 'status', 'priority'],
                'payment_method_status_priority_index'
            );

            $table->index(
                ['payment_provider_id', 'status', 'priority'],
                'payment_provider_status_priority_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_provider_method');
    }
};