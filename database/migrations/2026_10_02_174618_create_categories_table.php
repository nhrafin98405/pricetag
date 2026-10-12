<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Parent category for unlimited nested categories
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // Basic information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Media
            $table->string('image')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Display & status
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);

            // Timestamps & soft delete
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['parent_id', 'status']);
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};