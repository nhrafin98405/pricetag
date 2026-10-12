<?php

namespace App\Services\Payment\Drivers\Test;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Drivers\AbstractPaymentDriver;

class FakePaymentDriver extends AbstractPaymentDriver
{
    /**
     * Initiate a fake payment.
     */
    public function initiate(
        Order $order,
        Payment $payment,
        array $options = []
    ): array {
        return $this->success(
            'Fake payment initiated successfully.',
            [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'payment_number' => $payment->payment_number,
                'transaction_id' => 'TEST-' . strtoupper(uniqid()),
                'redirect_url' => null,
                'is_test' => true,
            ]
        );
    }

    /**
     * Verify a fake payment.
     */
    public function verify(
        Payment $payment,
        array $payload = []
    ): array {
        return $this->success(
            'Fake payment verified successfully.',
            [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'status' => 'paid',
                'is_test' => true,
            ]
        );
    }

    /**
     * Handle a fake webhook.
     */
    public function handleWebhook(
        array $payload,
        array $headers = []
    ): array {
        return $this->success(
            'Fake webhook handled successfully.',
            [
                'payment_id' => $payload['payment_id'] ?? null,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'status' => $payload['status'] ?? null,
                'payload' => $payload,
                'headers_received' => ! empty($headers),
                'is_test' => true,
            ]
        );
    }

    /**
     * Refund a fake payment.
     */
    public function refund(
        Payment $payment,
        ?float $amount = null,
        array $options = []
    ): array {
        $refundAmount = $amount ?? (float) $payment->amount;

        return $this->success(
            'Fake payment refunded successfully.',
            [
                'payment_id' => $payment->id,
                'refund_amount' => $refundAmount,
                'transaction_id' => $payment->transaction_id,
                'is_test' => true,
            ]
        );
    }
}
