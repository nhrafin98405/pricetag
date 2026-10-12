<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@pricetag.com')
            ->first();

        $coupons = [
            [
                'name' => 'Welcome 10% Off',
                'code' => 'WELCOME10',
                'description' => '10% discount for eligible orders.',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'maximum_discount_amount' => 500,
                'minimum_order_amount' => 1000,
                'minimum_quantity' => null,
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 1,
                'is_first_order_only' => true,
                'is_free_shipping' => false,
                'individual_use' => true,
                'exclude_sale_items' => false,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'status' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Flat 500 Off',
                'code' => 'FLAT500',
                'description' => 'Flat BDT 500 discount on eligible orders.',
                'discount_type' => 'fixed',
                'discount_value' => 500,
                'maximum_discount_amount' => null,
                'minimum_order_amount' => 3000,
                'minimum_quantity' => null,
                'usage_limit' => 500,
                'usage_limit_per_customer' => 2,
                'is_first_order_only' => false,
                'is_free_shipping' => false,
                'individual_use' => false,
                'exclude_sale_items' => false,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'status' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'Premium 15% Off',
                'code' => 'PREMIUM15',
                'description' => '15% discount for premium orders.',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'maximum_discount_amount' => 1000,
                'minimum_order_amount' => 5000,
                'minimum_quantity' => null,
                'usage_limit' => 300,
                'usage_limit_per_customer' => 1,
                'is_first_order_only' => false,
                'is_free_shipping' => false,
                'individual_use' => false,
                'exclude_sale_items' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'status' => true,
                'sort_order' => 3,
            ],

            [
                'name' => 'Free Shipping',
                'code' => 'FREESHIP',
                'description' => 'Free shipping on eligible orders.',
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'maximum_discount_amount' => null,
                'minimum_order_amount' => 2000,
                'minimum_quantity' => null,
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 3,
                'is_first_order_only' => false,
                'is_free_shipping' => true,
                'individual_use' => false,
                'exclude_sale_items' => false,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                [
                    'code' => $couponData['code'],
                ],
                array_merge(
                    $couponData,
                    [
                        'created_by' => $admin?->id,
                    ]
                )
            );
        }
    }
}