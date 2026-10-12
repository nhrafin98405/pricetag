
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // Basic information
            $table->string('name');
            $table->string('code', 100)->unique();
            $table->text('description')->nullable();

            // Discount configuration
            $table->string('discount_type', 30)->default('percentage')->index();
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('maximum_discount_amount', 12, 2)->nullable();
            $table->decimal('minimum_order_amount', 12, 2)->default(0);
            $table->decimal('minimum_quantity', 12, 2)->nullable();

            // Optional usage restrictions
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('usage_count')->default(0);

            // Eligibility
            $table->boolean('is_first_order_only')->default(false)->index();
            $table->boolean('is_free_shipping')->default(false)->index();
            $table->boolean('individual_use')->default(false);
            $table->boolean('exclude_sale_items')->default(false);

            // Schedule
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();

            // Administration
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['status', 'starts_at', 'expires_at'],
                'coupons_status_schedule_index'
            );

            $table->index(
                ['status', 'sort_order'],
                'coupons_status_sort_index'
            );

            $table->index(
                ['discount_type', 'status'],
                'coupons_discount_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
