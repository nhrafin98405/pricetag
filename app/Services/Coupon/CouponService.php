<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CouponService
{
    /**
     * Find an active coupon by code.
     */
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::query()
            ->whereRaw('LOWER(code) = ?', [strtolower(trim($code))])
            ->first();
    }

    /**
     * Validate a coupon for a customer/cart.
     *
     * Expected cart data:
     * [
     *     'subtotal' => 2500,
     *     'quantity' => 3,
     *     'has_sale_items' => false,
     *     'shipping_total' => 100,
     * ]
     */
    public function validate(
        Coupon $coupon,
        ?User $user = null,
        array $cart = []
    ): array {
        $subtotal = round((float) ($cart['subtotal'] ?? 0), 2);
        $quantity = (int) ($cart['quantity'] ?? 0);
        $hasSaleItems = (bool) ($cart['has_sale_items'] ?? false);

        if (! $coupon->is_currently_valid) {
            throw new InvalidArgumentException(
                'This coupon is not currently active.'
            );
        }

        if ($coupon->is_usage_limit_reached) {
            throw new InvalidArgumentException(
                'This coupon has reached its usage limit.'
            );
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            throw new InvalidArgumentException(
                'The minimum order amount for this coupon is ' .
                number_format((float) $coupon->minimum_order_amount, 2) .
                '.'
            );
        }

        if (
            $coupon->minimum_quantity !== null
            && $quantity < (float) $coupon->minimum_quantity
        ) {
            throw new InvalidArgumentException(
                'The minimum quantity required for this coupon is ' .
                (float) $coupon->minimum_quantity .
                '.'
            );
        }

        if ($coupon->exclude_sale_items && $hasSaleItems) {
            throw new InvalidArgumentException(
                'This coupon cannot be used with sale items.'
            );
        }

        if ($coupon->is_first_order_only) {
            if (! $user) {
                throw new InvalidArgumentException(
                    'This coupon is available only for first orders.'
                );
            }

            $hasPreviousOrder = $user->orders()
                ->whereNotIn('status', ['cancelled', 'failed'])
                ->exists();

            if ($hasPreviousOrder) {
                throw new InvalidArgumentException(
                    'This coupon is available only for your first order.'
                );
            }
        }

        if ($coupon->usage_limit_per_customer !== null) {
            if (! $user) {
                throw new InvalidArgumentException(
                    'You must be logged in to use this coupon.'
                );
            }

            $customerUsageCount = $coupon->usages()
                ->where('user_id', $user->id)
                ->whereIn('status', ['applied', 'processed'])
                ->count();

            if (
                $customerUsageCount >=
                $coupon->usage_limit_per_customer
            ) {
                throw new InvalidArgumentException(
                    'You have reached the usage limit for this coupon.'
                );
            }
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'subtotal' => $subtotal,
            'quantity' => $quantity,
        ];
    }

    /**
     * Calculate coupon discount.
     */
    public function calculateDiscount(
        Coupon $coupon,
        float $subtotal
    ): float {
        $subtotal = round(max(0, $subtotal), 2);

        if ($subtotal <= 0) {
            return 0.00;
        }

        $discountValue = round(
            max(0, (float) $coupon->discount_value),
            2
        );

        $discount = match ($coupon->discount_type) {
            'percentage' => $subtotal * ($discountValue / 100),

            'fixed',
            'fixed_amount',
            'amount' => min($discountValue, $subtotal),

            default => throw new InvalidArgumentException(
                "Unsupported coupon discount type [{$coupon->discount_type}]."
            ),
        };

        if ($coupon->maximum_discount_amount !== null) {
            $discount = min(
                $discount,
                (float) $coupon->maximum_discount_amount
            );
        }

        return round(
            min($discount, $subtotal),
            2
        );
    }

    /**
     * Validate and calculate a coupon in one operation.
     */
    public function apply(
        string $code,
        ?User $user = null,
        array $cart = []
    ): array {
        $coupon = $this->findByCode($code);

        if (! $coupon) {
            throw new InvalidArgumentException(
                'Coupon code is invalid.'
            );
        }

        $validation = $this->validate(
            $coupon,
            $user,
            $cart
        );

        $discount = $this->calculateDiscount(
            $coupon,
            (float) ($cart['subtotal'] ?? 0)
        );

        $shippingTotal = round(
            max(0, (float) ($cart['shipping_total'] ?? 0)),
            2
        );

        $freeShippingDiscount = $coupon->is_free_shipping
            ? $shippingTotal
            : 0.00;

        $totalDiscount = round(
            $discount + $freeShippingDiscount,
            2
        );

        return [
            'valid' => true,
            'coupon' => $coupon,
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
            'free_shipping_discount' => $freeShippingDiscount,
            'total_discount' => $totalDiscount,
            'subtotal' => $validation['subtotal'],
            'quantity' => $validation['quantity'],
        ];
    }

    /**
     * Record a successful coupon usage.
     */
    public function recordUsage(
        Coupon $coupon,
        ?User $user,
        ?int $orderId,
        float $discountAmount,
        array $metadata = []
    ) {
        return DB::transaction(function () use (
            $coupon,
            $user,
            $orderId,
            $discountAmount,
            $metadata
        ) {
            $coupon = Coupon::query()
                ->lockForUpdate()
                ->findOrFail($coupon->id);

            if ($coupon->is_usage_limit_reached) {
                throw new InvalidArgumentException(
                    'This coupon has reached its usage limit.'
                );
            }

            if (
                $coupon->usage_limit_per_customer !== null
                && $user
            ) {
                $customerUsageCount = $coupon->usages()
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['applied', 'processed'])
                    ->lockForUpdate()
                    ->count();

                if (
                    $customerUsageCount >=
                    $coupon->usage_limit_per_customer
                ) {
                    throw new InvalidArgumentException(
                        'You have reached the usage limit for this coupon.'
                    );
                }
            }

            $usage = $coupon->usages()->create([
                'user_id' => $user?->id,
                'order_id' => $orderId,
                'coupon_code' => $coupon->code,
                'discount_amount' => round(
                    max(0, $discountAmount),
                    2
                ),
                'currency' => 'BDT',
                'status' => 'applied',
                'used_at' => now(),
                'metadata' => $metadata ?: null,
            ]);

            $coupon->increment('usage_count');

            return $usage;
        });
    }
}