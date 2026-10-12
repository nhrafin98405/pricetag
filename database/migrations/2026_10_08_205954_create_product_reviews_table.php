<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->foreignId('order_item_id')
                ->nullable()
                ->constrained('order_items')
                ->nullOnDelete();

            $table->unsignedTinyInteger('rating');

            $table->string('title', 255)
                ->nullable();

            $table->text('body')
                ->nullable();

            $table->boolean('is_verified_purchase')
                ->default(false)
                ->index();

            $table->boolean('is_featured')
                ->default(false)
                ->index();

            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->text('admin_reply')
                ->nullable();

            $table->foreignId('replied_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('replied_at')
                ->nullable();

            $table->timestamp('published_at')
                ->nullable()
                ->index();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(
                ['product_id', 'status', 'published_at'],
                'product_reviews_product_status_published_index'
            );

            $table->index(
                ['user_id', 'status'],
                'product_reviews_user_status_index'
            );

            $table->index(
                ['order_id', 'order_item_id'],
                'product_reviews_order_item_index'
            );

            $table->index(
                ['rating', 'status'],
                'product_reviews_rating_status_index'
            );

            $table->index(
                ['status', 'is_featured', 'published_at'],
                'product_reviews_status_featured_published_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};