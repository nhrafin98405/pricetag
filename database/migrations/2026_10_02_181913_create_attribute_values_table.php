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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Attribute Relationship
            |--------------------------------------------------------------------------
            */
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Value Information
            |--------------------------------------------------------------------------
            */
            $table->string('value');

            $table->string('slug');

            /*
            |--------------------------------------------------------------------------
            | Optional Display Data
            |--------------------------------------------------------------------------
            |
            | Useful for color-type attributes.
            | Example:
            | #000000
            |
            */
            $table->string('color_code', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Display & Status
            |--------------------------------------------------------------------------
            */
            $table->boolean('status')
                ->default(true)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Timestamps & Soft Delete
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Unique Value Per Attribute
            |--------------------------------------------------------------------------
            */
            $table->unique(
                ['attribute_id', 'slug'],
                'attribute_values_attribute_slug_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['attribute_id', 'status', 'sort_order'],
                'attribute_values_attribute_status_sort_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};