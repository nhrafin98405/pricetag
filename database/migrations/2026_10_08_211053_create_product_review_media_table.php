<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_review_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_review_id')
                ->constrained('product_reviews')
                ->cascadeOnDelete();

            $table->string('type', 20)
                ->default('image')
                ->index();

            $table->string('disk', 50)
                ->default('public');

            $table->string('file_path');

            $table->string('thumbnail')
                ->nullable();

            $table->string('title')
                ->nullable();

            $table->string('alt_text')
                ->nullable();

            $table->string('mime_type')
                ->nullable();

            $table->unsignedBigInteger('file_size')
                ->nullable();

            $table->decimal('duration', 10, 2)
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_primary')
                ->default(false);

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->timestamps();

            $table->index(
                ['product_review_id', 'type', 'status'],
                'product_review_media_review_type_status_index'
            );

            $table->index(
                ['product_review_id', 'sort_order'],
                'product_review_media_review_sort_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_review_media');
    }
};