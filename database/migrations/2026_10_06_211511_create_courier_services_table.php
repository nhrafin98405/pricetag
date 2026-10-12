<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('courier_provider_id')
                ->constrained('courier_providers')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug', 120);
            $table->string('code', 80);

            $table->string('type', 30)
                ->default('standard')
                ->index();

            $table->text('description')->nullable();

            $table->decimal('base_rate', 12, 2)
                ->default(0);

            $table->decimal('inside_city_rate', 12, 2)
                ->nullable();

            $table->decimal('outside_city_rate', 12, 2)
                ->nullable();

            $table->decimal('outside_dhaka_rate', 12, 2)
                ->nullable();

            $table->decimal('cod_fee', 12, 2)
                ->default(0);

            $table->decimal('cod_percentage', 5, 2)
                ->default(0);

            $table->unsignedInteger('estimated_delivery_days')
                ->nullable();

            $table->boolean('supports_cod')
                ->default(true)
                ->index();

            $table->boolean('supports_return')
                ->default(true)
                ->index();

            $table->boolean('supports_exchange')
                ->default(false)
                ->index();

            $table->boolean('supports_tracking')
                ->default(true)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(
                ['courier_provider_id', 'slug'],
                'courier_services_provider_slug_unique'
            );

            $table->unique(
                ['courier_provider_id', 'code'],
                'courier_services_provider_code_unique'
            );

            $table->index(
                ['courier_provider_id', 'status', 'sort_order'],
                'courier_services_provider_status_sort_index'
            );

            $table->index(
                ['courier_provider_id', 'type', 'status'],
                'courier_services_provider_type_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_services');
    }
};