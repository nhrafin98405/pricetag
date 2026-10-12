<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // Authenticated customer cart
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            // Guest cart identification
            $table->string('session_id', 191)->nullable()->index();

            // Cart lifecycle
            $table->string('status', 30)
                ->default('active')
                ->index();

            // Currency
            $table->string('currency', 10)->default('BDT');

            // Applied coupon reference
            $table->string('coupon_code', 100)->nullable()->index();

            // Cart totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            // Last activity helps identify abandoned carts
            $table->timestamp('last_activity_at')
                ->nullable()
                ->index();

            $table->timestamps();

            /*
             * One active cart per authenticated customer.
             *
             * MySQL allows multiple NULL values in a unique index,
             * so guest carts can still have their own session IDs.
             */
            $table->unique(
                ['user_id', 'status'],
                'carts_user_status_unique'
            );

            $table->index(
                ['status', 'last_activity_at'],
                'carts_status_activity_index'
            );

            $table->index(
                ['session_id', 'status'],
                'carts_session_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};