<?php

namespace App\Services\Review;

use App\Models\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class ProductReviewListingService
{
    public function forProduct(
        int $productId,
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        if ($productId < 1) {
            throw new InvalidArgumentException(
                'A valid product ID is required.'
            );
        }

        if ($perPage < 1 || $perPage > 100) {
            throw new InvalidArgumentException(
                'Per-page must be between 1 and 100.'
            );
        }

        $rating = $filters['rating'] ?? null;

        if (
            $rating !== null
            && (
                filter_var($rating, FILTER_VALIDATE_INT) === false
                || (int) $rating < 1
                || (int) $rating > 5
            )
        ) {
            throw new InvalidArgumentException(
                'Rating filter must be between 1 and 5.'
            );
        }

        $query = ProductReview::query()
            ->published()
            ->where('product_id', $productId)
            ->with([
                'user:id,name',
                'media' => function ($query) {
                    $query->where('status', true);
                },
            ]);

        if ($rating !== null) {
            $query->where('rating', (int) $rating);
        }

        if (!empty($filters['verified_purchase'])) {
            $query->where('is_verified_purchase', true);
        }

        if (!empty($filters['with_media'])) {
            $query->whereHas('media', function ($mediaQuery) {
                $mediaQuery->where('status', true);
            });
        }

        $sort = $filters['sort'] ?? 'newest';

        switch ($sort) {
            case 'oldest':
                $query->orderBy('published_at')
                    ->orderBy('id');
                break;

            case 'highest_rating':
                $query->orderByDesc('rating')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id');
                break;

            case 'lowest_rating':
                $query->orderBy('rating')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id');
                break;

            case 'newest':
                $query->orderByDesc('published_at')
                    ->orderByDesc('id');
                break;

            default:
                throw new InvalidArgumentException(
                    'Unsupported review sort option.'
                );
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    public function forAdmin(
        array $filters = [],
        int $perPage = 20
    ): LengthAwarePaginator {
        if ($perPage < 1 || $perPage > 100) {
            throw new InvalidArgumentException(
                'Per-page must be between 1 and 100.'
            );
        }

        $query = ProductReview::query()
            ->with(['product:id,name', 'user:id,name']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', (int) $filters['product_id']);
        }

        if (!empty($filters['status'])) {
            $allowedStatuses = [
                'pending',
                'approved',
                'rejected',
            ];

            if (!in_array($filters['status'], $allowedStatuses, true)) {
                throw new InvalidArgumentException(
                    'Unsupported review status.'
                );
            }

            $query->where('status', $filters['status']);
        }

        if (isset($filters['rating'])) {
            $rating = filter_var(
                $filters['rating'],
                FILTER_VALIDATE_INT
            );

            if ($rating === false || $rating < 1 || $rating > 5) {
                throw new InvalidArgumentException(
                    'Rating filter must be between 1 and 5.'
                );
            }

            $query->where('rating', $rating);
        }

        if (isset($filters['verified_purchase'])) {
            $query->where(
                'is_verified_purchase',
                filter_var(
                    $filters['verified_purchase'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}