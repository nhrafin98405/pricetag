<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('label', 50)->nullable();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('company_name')->nullable();

            $table->string('phone', 30);
            $table->string('alternate_phone', 30)->nullable();

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();

            $table->string('area')->nullable();
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('country', 100)->default('Bangladesh');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default_shipping')
                ->default(false)
                ->index();

            $table->boolean('is_default_billing')
                ->default(false)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->timestamps();

            $table->softDeletes();

            $table->index(
                ['user_id', 'status'],
                'customer_addresses_user_status_index'
            );

            $table->index(
                ['user_id', 'is_default_shipping'],
                'customer_addresses_user_default_shipping_index'
            );

            $table->index(
                ['user_id', 'is_default_billing'],
                'customer_addresses_user_default_billing_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};