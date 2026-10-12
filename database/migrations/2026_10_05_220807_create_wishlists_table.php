<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name', 100);

            $table->string('slug', 120);

            $table->boolean('is_default')
                ->default(false)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
             * A customer cannot have duplicate wishlist names/slugs.
             */
            $table->unique(
                ['user_id', 'slug'],
                'wishlists_user_slug_unique'
            );

            $table->index(
                ['user_id', 'status'],
                'wishlists_user_status_index'
            );

            $table->index(
                ['user_id', 'is_default'],
                'wishlists_user_default_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};