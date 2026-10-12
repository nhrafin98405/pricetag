<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('customer_code', 50)->unique();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('phone', 30)->nullable()->index();
            $table->string('alternate_phone', 30)->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('gender', 30)->nullable();

            $table->string('company_name')->nullable();

            $table->text('notes')->nullable();

            $table->string('avatar')->nullable();

            $table->boolean('marketing_email_opt_in')
                ->default(false)
                ->index();

            $table->boolean('marketing_sms_opt_in')
                ->default(false)
                ->index();

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->timestamp('last_order_at')->nullable()->index();

            $table->timestamps();

            $table->softDeletes();

            $table->index(
                ['status', 'created_at'],
                'customer_profiles_status_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};