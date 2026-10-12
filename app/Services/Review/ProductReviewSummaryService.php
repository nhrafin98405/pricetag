<?php

namespace App\Services\Review;

use App\Models\ProductReview;
use Illuminate\Support\Collection;

class ProductReviewSummaryService
{
    /**
     * Get the published review summary for a product.
     */
    public function forProduct(int $productId): array
    {
        $reviews = ProductReview::query()
            ->published()
            ->where('product_id', $productId)
            ->select(['id', 'rating', 'is_verified_purchase'])
            ->get();

        $totalReviews = $reviews->count();

        $ratingCounts = $this->ratingCounts($reviews);

        return [
            'product_id' => $productId,
            'total_reviews' => $totalReviews,
            'average_rating' => $totalReviews > 0
                ? round((float) $reviews->avg('rating'), 2)
                : 0.0,
            'verified_purchase_count' => $reviews
                ->where('is_verified_purchase', true)
                ->count(),
            'rating_counts' => $ratingCounts,
            'rating_percentages' => $this->ratingPercentages(
                $ratingCounts,
                $totalReviews
            ),
        ];
    }

    /**
     * Return the number of published reviews for each rating.
     *
     * @return array<int, int>
     */
    private function ratingCounts(Collection $reviews): array
    {
        $counts = [];

        for ($rating = 1; $rating <= 5; $rating++) {
            $counts[$rating] = $reviews
                ->where('rating', $rating)
                ->count();
        }

        return $counts;
    }

    /**
     * Return each rating's percentage of all published reviews.
     *
     * @param array<int, int> $ratingCounts
     * @return array<int, float>
     */
    private function ratingPercentages(
        array $ratingCounts,
        int $totalReviews
    ): array {
        $percentages = [];

        for ($rating = 1; $rating <= 5; $rating++) {
            $percentages[$rating] = $totalReviews > 0
                ? round(($ratingCounts[$rating] / $totalReviews) * 100, 2)
                : 0.0;
        }

        return $percentages;
    }
}