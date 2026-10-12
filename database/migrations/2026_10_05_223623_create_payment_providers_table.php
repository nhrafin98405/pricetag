<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('code', 50)->unique();

            // gateway / direct / offline
            $table->string('type', 30)->default('gateway')->index();

            // Laravel payment driver / integration identifier
            $table->string('driver', 100)->nullable()->index();

            $table->text('description')->nullable();

            // Sandbox / production mode
            $table->boolean('is_test_mode')->default(true);

            // Provider availability
            $table->boolean('status')->default(true)->index();

            // Admin display order
            $table->unsignedInteger('sort_order')->default(0);

            // Encrypted API credentials/configuration
            $table->text('credentials')->nullable();

            // Non-sensitive provider configuration
            $table->json('settings')->nullable();

            // Extra provider metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['status', 'sort_order'],
                'payment_providers_status_sort_index'
            );

            $table->index(
                ['type', 'status'],
                'payment_providers_type_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};