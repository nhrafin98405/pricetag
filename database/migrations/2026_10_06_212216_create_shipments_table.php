<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Core Relationships
            |--------------------------------------------------------------------------
            */
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

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Shipment Identification
            |--------------------------------------------------------------------------
            */
            $table->string('shipment_number', 80)
                ->unique();

            $table->string('tracking_number', 150)
                ->nullable()
                ->index();

            $table->string('provider_shipment_id', 150)
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->string('fulfillment_status', 30)
                ->default('unfulfilled')
                ->index();

            $table->string('delivery_status', 30)
                ->default('pending')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Shipment Type
            |--------------------------------------------------------------------------
            */
            $table->string('type', 30)
                ->default('delivery')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Recipient Snapshot
            |--------------------------------------------------------------------------
            */
            $table->string('recipient_name');
            $table->string('recipient_phone', 50)
                ->nullable()
                ->index();

            $table->string('recipient_email')
                ->nullable();

            $table->string('address_line_1');
            $table->string('address_line_2')
                ->nullable();

            $table->string('area')
                ->nullable();

            $table->string('city')
                ->nullable();

            $table->string('state')
                ->nullable();

            $table->string('postal_code')
                ->nullable();

            $table->string('country')
                ->default('Bangladesh');

            /*
            |--------------------------------------------------------------------------
            | Package Information
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('package_count')
                ->default(1);

            $table->decimal('weight', 10, 3)
                ->nullable();

            $table->decimal('length', 10, 2)
                ->nullable();

            $table->decimal('width', 10, 2)
                ->nullable();

            $table->decimal('height', 10, 2)
                ->nullable();

            $table->string('package_type', 50)
                ->nullable();

            $table->text('package_description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Financial / COD
            |--------------------------------------------------------------------------
            */
            $table->decimal('shipping_cost', 12, 2)
                ->default(0);

            $table->decimal('cod_amount', 12, 2)
                ->default(0);

            $table->decimal('cod_collected_amount', 12, 2)
                ->default(0);

            $table->decimal('insurance_amount', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Delivery Dates
            |--------------------------------------------------------------------------
            */
            $table->timestamp('scheduled_at')
                ->nullable()
                ->index();

            $table->timestamp('shipped_at')
                ->nullable()
                ->index();

            $table->timestamp('estimated_delivery_at')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable()
                ->index();

            $table->timestamp('cancelled_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Courier Response / Extra Data
            |--------------------------------------------------------------------------
            */
            $table->json('provider_response')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->text('customer_note')
                ->nullable();

            $table->text('admin_note')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['order_id', 'status', 'created_at'],
                'shipments_order_status_created_index'
            );

            $table->index(
                ['courier_provider_id', 'status', 'created_at'],
                'shipments_provider_status_created_index'
            );

            $table->index(
                ['warehouse_id', 'status', 'created_at'],
                'shipments_warehouse_status_created_index'
            );

            $table->index(
                ['delivery_status', 'estimated_delivery_at'],
                'shipments_delivery_estimated_index'
            );

            $table->index(
                ['fulfillment_status', 'created_at'],
                'shipments_fulfillment_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};