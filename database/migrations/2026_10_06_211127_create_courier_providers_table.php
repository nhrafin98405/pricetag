<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_providers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug', 120)->unique();
            $table->string('code', 50)->unique();

            $table->string('type', 30)
                ->default('courier')
                ->index();

            $table->string('driver', 100)->nullable();

            $table->text('description')->nullable();

            $table->string('website')->nullable();
            $table->string('support_phone', 50)->nullable();
            $table->string('support_email')->nullable();

            $table->boolean('is_test_mode')
                ->default(true)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->boolean('is_default')
                ->default(false)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['status', 'sort_order'],
                'courier_providers_status_sort_index'
            );

            $table->index(
                ['status', 'is_default'],
                'courier_providers_status_default_index'
            );

            $table->index(
                ['type', 'status'],
                'courier_providers_type_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_providers');
    }
};