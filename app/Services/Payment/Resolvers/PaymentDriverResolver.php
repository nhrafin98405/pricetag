<?php

namespace App\Services\Payment\Resolvers;

use App\Services\Payment\Contracts\PaymentDriverInterface;
use InvalidArgumentException;

class PaymentDriverResolver
{
    /**
     * Registered payment drivers.
     *
     * @var array<string, class-string<PaymentDriverInterface>>
     */
    protected array $drivers = [];

    /**
     * Register a payment driver.
     */
    public function register(
        string $code,
        string $driverClass
    ): static {
        if (! is_a($driverClass, PaymentDriverInterface::class, true)) {
            throw new InvalidArgumentException(
                "Driver [{$driverClass}] must implement PaymentDriverInterface."
            );
        }

        $this->drivers[$code] = $driverClass;

        return $this;
    }

    /**
     * Resolve a registered driver.
     */
    public function resolve(string $code): PaymentDriverInterface
    {
        $driverClass = $this->drivers[$code] ?? null;

        if (! $driverClass) {
            throw new InvalidArgumentException(
                "Payment driver [{$code}] is not registered."
            );
        }

        return app($driverClass);
    }

    /**
     * Check whether a driver is registered.
     */
    public function has(string $code): bool
    {
        return isset($this->drivers[$code]);
    }

    /**
     * Get all registered drivers.
     */
    public function all(): array
    {
        return $this->drivers;
    }
}