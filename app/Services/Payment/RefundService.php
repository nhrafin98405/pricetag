<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProvider;
use App\Models\PaymentRefund;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class RefundService
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function refund(
        PaymentProvider $provider,
        Payment $payment,
        ?float $amount = null,
        array $options = []
    ): array {
        return DB::transaction(function () use (
            $provider,
            $payment,
            $amount,
            $options
        ) {
            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $order = $payment->order;

            if (! $order) {
                throw new RuntimeException(
                    'Cannot refund a payment without an order.'
                );
            }

            if ($payment->status === 'refunded') {
                throw new InvalidArgumentException(
                    'This payment has already been fully refunded.'
                );
            }

            if ($payment->status !== 'paid') {
                throw new InvalidArgumentException(
                    'Only paid payments can be refunded.'
                );
            }

            $paymentAmount = (float) $payment->amount;

            $refundedAmount = (float) PaymentRefund::query()
                ->where('payment_id', $payment->id)
                ->whereIn('status', ['pending', 'processed'])
                ->sum('amount');

            $remainingAmount = round(
                $paymentAmount - $refundedAmount,
                2
            );

            if ($remainingAmount <= 0) {
                throw new InvalidArgumentException(
                    'This payment has already been fully refunded.'
                );
            }

            $refundAmount = $amount === null
                ? $remainingAmount
                : round($amount, 2);

            if ($refundAmount <= 0) {
                throw new InvalidArgumentException(
                    'Refund amount must be greater than zero.'
                );
            }

            if ($refundAmount > $remainingAmount) {
                throw new InvalidArgumentException(
                    "Refund amount cannot exceed the remaining refundable amount of {$remainingAmount}."
                );
            }

            $refund = PaymentRefund::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'refund_number' => $this->generateRefundNumber(),
                'provider' => $provider->code,
                'method' => $payment->method,
                'status' => 'pending',
                'currency' => $payment->currency,
                'amount' => $refundAmount,
                'transaction_id' => $payment->transaction_id,
                'reason' => $options['reason'] ?? null,
                'notes' => $options['notes'] ?? null,
                'metadata' => $options['metadata'] ?? null,
                'initiated_at' => now(),
            ]);

            $result = $this->paymentService->refund(
                $provider,
                $payment,
                $refundAmount,
                $options
            );

            if (! ($result['success'] ?? false)) {
                $data = $result['data'] ?? [];

                $refund->update([
                    'status' => 'failed',
                    'failure_code' => $data['failure_code'] ?? null,
                    'failure_message' => $result['message'] ?? 'Refund failed.',
                    'provider_response' => $data,
                    'failed_at' => now(),
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Refund failed.',
                    'data' => [
                        'refund' => $refund->fresh(),
                    ],
                ];
            }

            $data = $result['data'] ?? [];

            $refund->update([
                'status' => 'processed',
                'provider_refund_id' =>
                $data['provider_refund_id']
                    ?? $data['refund_id']
                    ?? null,
                'provider_response' => $data,
                'processed_at' => now(),
            ]);

            $totalRefunded = (float) PaymentRefund::query()
                ->where('payment_id', $payment->id)
                ->where('status', 'processed')
                ->sum('amount');

            if ($totalRefunded >= $paymentAmount) {
                $payment->update([
                    'status' => 'refunded',
                    'refunded_at' => $payment->refunded_at ?? now(),
                ]);
            }

            return [
                'success' => true,
                'message' => 'Payment refund processed successfully.',
                'data' => [
                    'refund' => $refund->fresh(),
                    'total_refunded' => round($totalRefunded, 2),
                    'remaining_refundable' => round(
                        max(0, $paymentAmount - $totalRefunded),
                        2
                    ),
                ],
            ];
        });
    }

    protected function generateRefundNumber(): string
    {
        do {
            $number = 'RFN-' . now()->format('YmdHis') . '-' .
                strtoupper(bin2hex(random_bytes(4)));
        } while (
            PaymentRefund::query()
            ->where('refund_number', $number)
            ->exists()
        );

        return $number;
    }
}
