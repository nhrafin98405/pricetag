<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\Contracts\PaymentDriverInterface;

abstract class AbstractPaymentDriver implements PaymentDriverInterface
{
    /**
     * Provider configuration.
     */
    protected array $config = [];

    /**
     * Set provider configuration.
     */
    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get a configuration value.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Build a standardized successful response.
     */
    protected function success(
        string $message,
        array $data = []
    ): array {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Build a standardized failed response.
     */
    protected function failure(
        string $message,
        array $data = []
    ): array {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}