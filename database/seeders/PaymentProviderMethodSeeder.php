<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class PaymentProviderMethodSeeder extends Seeder
{
    public function run(): void
    {
        $providerMethods = [
            'bkash' => [
                'bkash',
            ],

            'nagad' => [
                'nagad',
            ],

            'rocket' => [
                'rocket',
            ],

            'sslcommerz' => [
                'visa',
                'mastercard',
                'amex',
                'unionpay',
                'bkash',
                'nagad',
            ],

            'shurjopay' => [
                'visa',
                'mastercard',
                'amex',
                'unionpay',
                'bkash',
                'nagad',
            ],

            'aamarpay' => [
                'visa',
                'mastercard',
                'amex',
                'unionpay',
                'bkash',
                'nagad',
            ],

            'portwallet' => [
                'visa',
                'mastercard',
                'amex',
                'unionpay',
                'bkash',
                'nagad',
            ],

            'stripe' => [
                'visa',
                'mastercard',
                'amex',
            ],

            'cod' => [
                'cod',
            ],

            'bank_transfer' => [
                'bank_transfer',
            ],

            'manual' => [
                'manual',
            ],
        ];

        foreach ($providerMethods as $providerCode => $methodCodes) {
            $provider = PaymentProvider::where('code', $providerCode)->first();

            if (! $provider) {
                continue;
            }

            foreach ($methodCodes as $priority => $methodCode) {
                $method = PaymentMethod::where('code', $methodCode)->first();

                if (! $method) {
                    continue;
                }

                $provider->methods()->syncWithoutDetaching([
                    $method->id => [
                        'status' => true,
                        'is_default' => $priority === 0,
                        'priority' => $priority + 1,
                        'settings' => [
                            'test_mode' => true,
                        ],
                        'metadata' => [
                            'seeded' => true,
                        ],
                    ],
                ]);
            }
        }
    }
}