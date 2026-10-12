<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'bKash',
                'slug' => 'bkash',
                'code' => 'bkash',
                'type' => 'gateway',
                'driver' => 'bkash',
                'description' => 'bKash payment gateway integration.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 10,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Nagad',
                'slug' => 'nagad',
                'code' => 'nagad',
                'type' => 'gateway',
                'driver' => 'nagad',
                'description' => 'Nagad payment gateway integration.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 20,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Rocket',
                'slug' => 'rocket',
                'code' => 'rocket',
                'type' => 'gateway',
                'driver' => 'rocket',
                'description' => 'Rocket payment integration.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 30,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'SSLCommerz',
                'slug' => 'sslcommerz',
                'code' => 'sslcommerz',
                'type' => 'gateway',
                'driver' => 'sslcommerz',
                'description' => 'SSLCommerz multi-channel payment gateway.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 40,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'ShurjoPay',
                'slug' => 'shurjopay',
                'code' => 'shurjopay',
                'type' => 'gateway',
                'driver' => 'shurjopay',
                'description' => 'ShurjoPay payment gateway.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 50,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'aamarPay',
                'slug' => 'aamarpay',
                'code' => 'aamarpay',
                'type' => 'gateway',
                'driver' => 'aamarpay',
                'description' => 'aamarPay payment gateway.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 60,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'PortWallet',
                'slug' => 'portwallet',
                'code' => 'portwallet',
                'type' => 'gateway',
                'driver' => 'portwallet',
                'description' => 'PortWallet payment gateway.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 70,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'code' => 'stripe',
                'type' => 'gateway',
                'driver' => 'stripe',
                'description' => 'Stripe international card payment gateway.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 80,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Cash on Delivery',
                'slug' => 'cash-on-delivery',
                'code' => 'cod',
                'type' => 'offline',
                'driver' => 'cod',
                'description' => 'Cash payment collected by courier on delivery.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 90,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Bank Transfer',
                'slug' => 'bank-transfer',
                'code' => 'bank_transfer',
                'type' => 'offline',
                'driver' => 'bank_transfer',
                'description' => 'Direct bank transfer payment.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 100,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
            [
                'name' => 'Manual Payment',
                'slug' => 'manual-payment',
                'code' => 'manual',
                'type' => 'offline',
                'driver' => 'manual',
                'description' => 'Payment verified manually by administrator.',
                'is_test_mode' => true,
                'status' => true,
                'sort_order' => 110,
                'credentials' => [],
                'settings' => [
                    'currency' => 'BDT',
                ],
            ],
        ];

        foreach ($providers as $provider) {
            PaymentProvider::updateOrCreate(
                ['code' => $provider['code']],
                $provider
            );
        }
    }
}