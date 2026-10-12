<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Customer
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Order identification
            $table->string('order_number', 50)->unique();
            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_status', 30)->default('pending')->index();
            $table->string('fulfillment_status', 30)->default('unfulfilled')->index();

            // Currency
            $table->string('currency', 10)->default('BDT');

            // Customer snapshot
            $table->string('customer_name');
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_phone')->nullable()->index();

            // Billing address snapshot
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company_name')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_area')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->nullable();

            // Shipping address snapshot
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->string('shipping_company_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address_line_1')->nullable();
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_area')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country')->nullable();

            // Order totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            // Coupon
            $table->string('coupon_code', 100)->nullable()->index();

            // Customer notes / internal notes
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();

            // Source / channel
            $table->string('source', 30)->default('web')->index();

            // Important lifecycle timestamps
            $table->timestamp('placed_at')->nullable()->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Useful composite indexes
            $table->index(
                ['user_id', 'status', 'created_at'],
                'orders_user_status_created_index'
            );

            $table->index(
                ['status', 'payment_status', 'created_at'],
                'orders_status_payment_created_index'
            );

            $table->index(
                ['fulfillment_status', 'created_at'],
                'orders_fulfillment_created_index'
            );

            $table->index(
                ['source', 'created_at'],
                'orders_source_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};