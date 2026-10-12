<?php

namespace App\Providers;

use App\Services\Payment\Drivers\Test\FakePaymentDriver;
use App\Services\Payment\Resolvers\PaymentDriverResolver;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register payment services.
     */
    public function register(): void
    {
        $this->app->singleton(
            PaymentDriverResolver::class,
            function () {
                $resolver = new PaymentDriverResolver();

                $resolver->register(
                    'fake',
                    FakePaymentDriver::class
                );

                return $resolver;
            }
        );
    }

    /**
     * Bootstrap payment services.
     */
    public function boot(): void
    {
        //
    }
}