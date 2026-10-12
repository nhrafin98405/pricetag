<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Product Relationship
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Media Information
            |--------------------------------------------------------------------------
            |
            | Supported types:
            | image
            | video
            |
            */
            $table->string('type', 20)
                ->default('image')
                ->index();

            $table->string('disk', 50)
                ->default('public');

            $table->string('file_path');

            /*
            |--------------------------------------------------------------------------
            | Video Thumbnail
            |--------------------------------------------------------------------------
            |
            | Mainly used for video preview/thumbnail.
            | Can also be used for other media types if needed later.
            |
            */
            $table->string('thumbnail')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Media Metadata
            |--------------------------------------------------------------------------
            */
            $table->string('title')->nullable();

            $table->string('alt_text')->nullable();

            $table->string('mime_type', 100)->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Video Metadata
            |--------------------------------------------------------------------------
            |
            | Duration is stored in seconds.
            | Example:
            | 12.50 = 12.5 seconds
            |
            */
            $table->decimal('duration', 10, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_primary')
                ->default(false);

            $table->boolean('status')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Composite Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['product_id', 'type', 'status'],
                'product_media_product_type_status_index'
            );

            $table->index(
                ['product_id', 'sort_order'],
                'product_media_product_sort_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};