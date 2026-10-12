<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('coupon_id')
                ->constrained('coupons')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->string('coupon_code', 100);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->string('currency', 10)->default('BDT');

            $table->string('status', 30)
                ->default('applied')
                ->index();

            $table->timestamp('used_at')
                ->nullable()
                ->index();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['coupon_id', 'user_id', 'status'],
                'coupon_usages_coupon_user_status_index'
            );

            $table->index(
                ['coupon_id', 'order_id'],
                'coupon_usages_coupon_order_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'coupon_usages_user_created_index'
            );

            $table->index(
                ['coupon_code', 'status'],
                'coupon_usages_code_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};