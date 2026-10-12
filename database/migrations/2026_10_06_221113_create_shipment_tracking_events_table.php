<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_tracking_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->string('status', 50)->index();

            $table->string('title', 150)->nullable();
            $table->text('description')->nullable();

            $table->string('location', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();

            $table->string('event_code', 100)->nullable()->index();
            $table->string('provider_status', 100)->nullable()->index();

            $table->timestamp('occurred_at')->nullable()->index();

            $table->boolean('is_customer_visible')
                ->default(true)
                ->index();

            $table->boolean('is_current')
                ->default(false)
                ->index();

            $table->json('provider_response')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['shipment_id', 'occurred_at'],
                'shipment_tracking_events_shipment_occurred_index'
            );

            $table->index(
                ['shipment_id', 'is_current'],
                'shipment_tracking_events_shipment_current_index'
            );

            $table->index(
                ['shipment_id', 'is_customer_visible', 'occurred_at'],
                'shipment_tracking_events_customer_visible_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_events');
    }
};