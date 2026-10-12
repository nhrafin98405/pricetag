<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->string('from_status', 30)->nullable()->index();
            $table->string('to_status', 30)->index();

            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();

            $table->string('source', 30)
                ->default('system')
                ->index();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('changed_at')
                ->nullable()
                ->index();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['shipment_id', 'created_at'],
                'shipment_status_histories_shipment_created_index'
            );

            $table->index(
                ['shipment_id', 'to_status', 'created_at'],
                'shipment_status_histories_shipment_status_created_index'
            );

            $table->index(
                ['changed_by', 'created_at'],
                'shipment_status_histories_changed_by_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_status_histories');
    }
};