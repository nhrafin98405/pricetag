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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Attribute Information
            |--------------------------------------------------------------------------
            */
            $table->string('name');

            $table->string('slug')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Attribute Type
            |--------------------------------------------------------------------------
            |
            | Examples:
            | select, color, text, number
            |
            */
            $table->string('type', 30)
                ->default('select');

            $table->text('description')
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
            | Index
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['status', 'sort_order'],
                'attributes_status_sort_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};