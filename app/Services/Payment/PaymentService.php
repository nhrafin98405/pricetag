<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProvider;
use App\Services\Payment\Contracts\PaymentDriverInterface;
use App\Services\Payment\Resolvers\PaymentDriverResolver;
use InvalidArgumentException;

class PaymentService
{
    public function __construct(
        protected PaymentDriverResolver $resolver
    ) {}

    public function driver(
        PaymentProvider $provider
    ): PaymentDriverInterface {
        if (! $provider->driver) {
            throw new InvalidArgumentException(
                "Payment driver is not configured for provider [{$provider->code}]."
            );
        }

        $driver = $this->resolver->resolve($provider->driver);

        return $driver->setConfig([
            'credentials' => $provider->credentials ?? [],
            'settings' => $provider->settings ?? [],
            'metadata' => $provider->metadata ?? [],
            'is_test_mode' => $provider->is_test_mode,
        ]);
    }

    public function initiate(
        PaymentProvider $provider,
        Order $order,
        Payment $payment,
        array $options = []
    ): array {
        $result = $this
            ->driver($provider)
            ->initiate($order, $payment, $options);

        if ($result['success'] ?? false) {
            $data = $result['data'] ?? [];

            $paymentData = [
                'initiated_at' => $payment->initiated_at ?? now(),
            ];

            if (! empty($data['transaction_id'])) {
                $paymentData['transaction_id'] = $data['transaction_id'];
            }

            $payment->update($paymentData);
        }

        return $result;
    }

    public function verify(
        PaymentProvider $provider,
        Payment $payment,
        array $payload = []
    ): array {
        $result = $this
            ->driver($provider)
            ->verify($payment, $payload);

        if ($result['success'] ?? false) {
            $data = $result['data'] ?? [];
            $gatewayStatus = $data['status'] ?? null;

            if ($gatewayStatus === 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $payment->paid_at ?? now(),
                ]);

                $order = $payment->order;

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => $order->paid_at ?? now(),
                    ]);
                }
            }
        }

        return $result;
    }

    public function handleWebhook(
        PaymentProvider $provider,
        array $payload,
        array $headers = []
    ): array {
        $result = $this
            ->driver($provider)
            ->handleWebhook($payload, $headers);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $data = $result['data'] ?? [];

        $paymentId = $data['payment_id'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        $gatewayStatus = $data['status'] ?? null;

        if (! $paymentId && ! $transactionId) {
            return [
                'success' => false,
                'message' => 'Webhook did not contain a payment reference.',
                'data' => [],
            ];
        }

        $payment = null;

        if ($paymentId) {
            $payment = Payment::find($paymentId);
        }

        if (! $payment && $transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
        }

        if (! $payment) {
            return [
                'success' => false,
                'message' => 'Payment could not be found for webhook.',
                'data' => [
                    'payment_id' => $paymentId,
                    'transaction_id' => $transactionId,
                ],
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Idempotency
    |--------------------------------------------------------------------------
    |
    | If this payment has already reached a final paid state, a duplicate
    | webhook must not perform the paid transition again.
    |
    */

        if (
            $payment->status === 'paid'
            && $payment->paid_at
            && $gatewayStatus === 'paid'
        ) {
            return [
                'success' => true,
                'message' => 'Webhook already processed for this payment.',
                'data' => [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                    'already_processed' => true,
                ],
            ];
        }

        if ($gatewayStatus === 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            $order = $payment->order;

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => $order->paid_at ?? now(),
                ]);
            }
        }

        return $result;
    }

    public function refund(
        PaymentProvider $provider,
        Payment $payment,
        ?float $amount = null,
        array $options = []
    ): array {
        return $this
            ->driver($provider)
            ->refund($payment, $amount, $options);
    }
}
