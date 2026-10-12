<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_webhook_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('courier_provider_id')
                ->nullable()
                ->constrained('courier_providers')
                ->nullOnDelete();

            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('shipments')
                ->nullOnDelete();

            $table->string('event_id', 150)
                ->nullable()
                ->index();

            $table->string('event_type', 100)
                ->nullable()
                ->index();

            $table->string('provider_event_type', 100)
                ->nullable()
                ->index();

            $table->string('status', 30)
                ->default('received')
                ->index();

            $table->string('http_method', 20)
                ->nullable();

            $table->string('signature', 500)
                ->nullable();

            $table->boolean('signature_verified')
                ->default(false)
                ->index();

            $table->json('headers')->nullable();

            $table->json('payload')->nullable();

            $table->json('normalized_data')->nullable();

            $table->text('processing_error')->nullable();

            $table->unsignedInteger('attempts')
                ->default(0);

            $table->timestamp('received_at')
                ->nullable()
                ->index();

            $table->timestamp('processed_at')
                ->nullable()
                ->index();

            $table->timestamp('failed_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['courier_provider_id', 'status', 'created_at'],
                'courier_webhook_logs_provider_status_created_index'
            );

            $table->index(
                ['shipment_id', 'created_at'],
                'courier_webhook_logs_shipment_created_index'
            );

            $table->index(
                ['event_type', 'status', 'created_at'],
                'courier_webhook_logs_event_status_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_webhook_logs');
    }
};