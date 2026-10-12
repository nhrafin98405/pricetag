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
        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('refund_number', 80)->unique();

            $table->string('provider', 50)->index();

            $table->string('method', 50)->index();

            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->string('currency', 10)->default('BDT');

            $table->decimal('amount', 12, 2)->default(0);

            $table->string('transaction_id', 150)
                ->nullable()
                ->index();

            $table->string('provider_refund_id', 150)
                ->nullable()
                ->index();

            $table->string('reason', 255)->nullable();

            $table->text('notes')->nullable();

            $table->json('provider_response')->nullable();

            $table->json('metadata')->nullable();

            $table->string('failure_code', 100)->nullable();

            $table->text('failure_message')->nullable();

            $table->timestamp('initiated_at')->nullable()->index();

            $table->timestamp('processed_at')->nullable()->index();

            $table->timestamp('failed_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(
                ['payment_id', 'status', 'created_at'],
                'payment_refunds_payment_status_created_index'
            );

            $table->index(
                ['order_id', 'status', 'created_at'],
                'payment_refunds_order_status_created_index'
            );

            $table->index(
                ['provider', 'status', 'created_at'],
                'payment_refunds_provider_status_created_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_refunds');
    }
};
