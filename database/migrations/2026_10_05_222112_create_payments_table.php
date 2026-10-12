<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Order relationship
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Payment identification
            $table->string('payment_number', 80)->unique();

            // Provider-independent architecture
            $table->string('provider', 50)->index();
            $table->string('method', 50)->index();

            // Payment lifecycle
            $table->string('status', 30)
                ->default('pending')
                ->index();

            // Amount
            $table->string('currency', 10)->default('BDT');
            $table->decimal('amount', 12, 2)->default(0);

            // Provider transaction references
            $table->string('transaction_id', 150)->nullable()->index();
            $table->string('provider_transaction_id', 150)->nullable()->index();
            $table->string('authorization_code', 150)->nullable();

            // Provider response / gateway metadata
            $table->json('provider_response')->nullable();
            $table->json('metadata')->nullable();

            // Failure information
            $table->string('failure_code', 100)->nullable();
            $table->text('failure_message')->nullable();

            // Lifecycle timestamps
            $table->timestamp('initiated_at')->nullable()->index();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // Useful indexes
            $table->index(
                ['order_id', 'status', 'created_at'],
                'payments_order_status_created_index'
            );

            $table->index(
                ['provider', 'method', 'status'],
                'payments_provider_method_status_index'
            );

            $table->index(
                ['status', 'paid_at'],
                'payments_status_paid_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};