<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('courier_provider_id')
                ->nullable()
                ->constrained('courier_providers')
                ->nullOnDelete();

            $table->foreignId('courier_service_id')
                ->nullable()
                ->constrained('courier_services')
                ->nullOnDelete();

            $table->string('return_number', 80)->unique();

            $table->string('tracking_number', 150)
                ->nullable()
                ->index();

            $table->string('provider_return_id', 150)
                ->nullable()
                ->index();

            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->string('reason', 100)->nullable()->index();

            $table->text('notes')->nullable();

            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 50)->nullable()->index();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Bangladesh');

            $table->decimal('shipping_cost', 12, 2)->default(0);

            $table->timestamp('requested_at')->nullable()->index();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('in_transit_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->json('provider_response')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['shipment_id', 'status', 'created_at'],
                'shipment_returns_shipment_status_created_index'
            );

            $table->index(
                ['order_id', 'status', 'created_at'],
                'shipment_returns_order_status_created_index'
            );

            $table->index(
                ['courier_provider_id', 'status', 'created_at'],
                'shipment_returns_provider_status_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_returns');
    }
};