<?php

namespace App\Services\Review;

use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductReviewService
{
    /**
     * Create a new product review for a completed order item.
     */
    public function create(
        User $user,
        OrderItem $orderItem,
        int $rating,
        ?string $title = null,
        ?string $body = null,
        array $metadata = []
    ): ProductReview {
        return DB::transaction(function () use (
            $user,
            $orderItem,
            $rating,
            $title,
            $body,
            $metadata
        ) {
            $orderItem = OrderItem::query()
                ->with(['order', 'product', 'variant'])
                ->lockForUpdate()
                ->find($orderItem->id);

            if (! $orderItem) {
                throw new ModelNotFoundException(
                    'The selected order item could not be found.'
                );
            }

            if (! $orderItem->product_id) {
                throw new InvalidArgumentException(
                    'This order item is not linked to a product.'
                );
            }

            $order = $orderItem->order;

            if (! $order) {
                throw new InvalidArgumentException(
                    'This order item is not linked to an order.'
                );
            }

            if ((int) $order->user_id !== (int) $user->id) {
                throw new InvalidArgumentException(
                    'You can only review products from your own orders.'
                );
            }

            $this->validateRating($rating);

            if (! $this->isOrderEligibleForReview($order)) {
                throw new InvalidArgumentException(
                    'This order is not eligible for a product review.'
                );
            }

            $existingReview = ProductReview::query()
                ->where('user_id', $user->id)
                ->where('order_item_id', $orderItem->id)
                ->lockForUpdate()
                ->first();

            if ($existingReview) {
                throw new InvalidArgumentException(
                    'You have already reviewed this order item.'
                );
            }

            return ProductReview::create([
                'product_id' => $orderItem->product_id,
                'product_variant_id' => $orderItem->product_variant_id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,

                'rating' => $rating,
                'title' => $this->cleanText($title),
                'body' => $this->cleanText($body),

                'is_verified_purchase' => true,
                'is_featured' => false,
                'status' => 'pending',

                'admin_reply' => null,
                'replied_by' => null,
                'replied_at' => null,

                'published_at' => null,

                'metadata' => $metadata ?: null,
            ]);
        });
    }

    /**
     * Update a customer's own pending review.
     */
    public function update(
        User $user,
        ProductReview $review,
        int $rating,
        ?string $title = null,
        ?string $body = null,
        array $metadata = []
    ): ProductReview {
        return DB::transaction(function () use (
            $user,
            $review,
            $rating,
            $title,
            $body,
            $metadata
        ) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            if ((int) $review->user_id !== (int) $user->id) {
                throw new InvalidArgumentException(
                    'You can only update your own review.'
                );
            }

            if (! in_array($review->status, ['pending', 'rejected'], true)) {
                throw new InvalidArgumentException(
                    'Only pending or rejected reviews can be edited.'
                );
            }

            $this->validateRating($rating);

            $review->update([
                'rating' => $rating,
                'title' => $this->cleanText($title),
                'body' => $this->cleanText($body),

                'status' => 'pending',
                'published_at' => null,

                'metadata' => $metadata ?: null,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Approve and optionally publish a review.
     */
    public function approve(
        ProductReview $review,
        User $admin,
        bool $publish = true
    ): ProductReview {
        return DB::transaction(function () use (
            $review,
            $admin,
            $publish
        ) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            $review->update([
                'status' => 'approved',
                'published_at' => $publish ? ($review->published_at ?? now()) : null,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Reject a review.
     */
    public function reject(
        ProductReview $review,
        User $admin
    ): ProductReview {
        return DB::transaction(function () use ($review, $admin) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            $review->update([
                'status' => 'rejected',
                'published_at' => null,
                'is_featured' => false,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Publish an approved review.
     */
    public function publish(ProductReview $review): ProductReview
    {
        return DB::transaction(function () use ($review) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            if ($review->status !== 'approved') {
                throw new InvalidArgumentException(
                    'Only approved reviews can be published.'
                );
            }

            $review->update([
                'published_at' => $review->published_at ?? now(),
            ]);

            return $review->refresh();
        });
    }

    /**
     * Unpublish a review.
     */
    public function unpublish(ProductReview $review): ProductReview
    {
        return DB::transaction(function () use ($review) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            $review->update([
                'published_at' => null,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Add or update an admin reply.
     */
    public function reply(
        ProductReview $review,
        User $admin,
        string $reply
    ): ProductReview {
        $reply = trim($reply);

        if ($reply === '') {
            throw new InvalidArgumentException(
                'The admin reply cannot be empty.'
            );
        }

        return DB::transaction(function () use (
            $review,
            $admin,
            $reply
        ) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            $review->update([
                'admin_reply' => $reply,
                'replied_by' => $admin->id,
                'replied_at' => now(),
            ]);

            return $review->refresh();
        });
    }

    /**
     * Remove an admin reply.
     */
    public function removeReply(ProductReview $review): ProductReview
    {
        return DB::transaction(function () use ($review) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            $review->update([
                'admin_reply' => null,
                'replied_by' => null,
                'replied_at' => null,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Feature or unfeature a review.
     */
    public function setFeatured(
        ProductReview $review,
        bool $featured
    ): ProductReview {
        return DB::transaction(function () use ($review, $featured) {
            $review = ProductReview::query()
                ->lockForUpdate()
                ->find($review->id);

            if (! $review) {
                throw new ModelNotFoundException(
                    'The selected review could not be found.'
                );
            }

            if ($featured && ! $review->is_published) {
                throw new InvalidArgumentException(
                    'Only published reviews can be featured.'
                );
            }

            $review->update([
                'is_featured' => $featured,
            ]);

            return $review->refresh();
        });
    }

    /**
     * Validate a review rating.
     */
    public function validateRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException(
                'Review rating must be between 1 and 5.'
            );
        }
    }

    /**
     * Determine whether an order is eligible for review.
     */
    protected function isOrderEligibleForReview($order): bool
    {
        return in_array(
            $order->status,
            ['completed', 'delivered'],
            true
        );
    }

    /**
     * Basic text normalization.
     */
    protected function cleanText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}