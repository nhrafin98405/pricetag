<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentDriverInterface
{
    /**
     * Initialize a payment transaction.
     */
    public function initiate(
        Order $order,
        Payment $payment,
        array $options = []
    ): array;

    /**
     * Verify a payment transaction.
     */
    public function verify(
        Payment $payment,
        array $payload = []
    ): array;

    /**
     * Handle provider webhook/callback.
     */
    public function handleWebhook(
        array $payload,
        array $headers = []
    ): array;

    /**
     * Refund a payment.
     */
    public function refund(
        Payment $payment,
        ?float $amount = null,
        array $options = []
    ): array;
}