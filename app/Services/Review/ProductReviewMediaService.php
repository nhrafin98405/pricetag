<?php

namespace App\Services\Review;

use App\Models\ProductReview;
use App\Models\ProductReviewMedia;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductReviewMediaService
{
    /**
     * Add a media record to a review.
     *
     * File upload and MIME/size validation must happen
     * in the calling application layer.
     */
    public function add(
        ProductReview $review,
        array $data
    ): ProductReviewMedia {
        $type = $data['type'] ?? 'image';

        if (! in_array($type, ['image', 'video'], true)) {
            throw new InvalidArgumentException(
                'Review media type must be image or video.'
            );
        }

        foreach (['disk', 'file_path'] as $field) {
            if (
                ! isset($data[$field])
                || ! is_string($data[$field])
                || trim($data[$field]) === ''
            ) {
                throw new InvalidArgumentException(
                    "A valid {$field} is required."
                );
            }
        }

        if (
            isset($data['sort_order'])
            && (
                ! is_numeric($data['sort_order'])
                || (int) $data['sort_order'] < 0
            )
        ) {
            throw new InvalidArgumentException(
                'Sort order must be a non-negative integer.'
            );
        }

        if (
            isset($data['file_size'])
            && (
                ! is_numeric($data['file_size'])
                || (int) $data['file_size'] < 0
            )
        ) {
            throw new InvalidArgumentException(
                'File size must be a non-negative integer.'
            );
        }

        if (
            isset($data['duration'])
            && (
                ! is_numeric($data['duration'])
                || (float) $data['duration'] < 0
            )
        ) {
            throw new InvalidArgumentException(
                'Media duration must be a non-negative number.'
            );
        }

        return DB::transaction(function () use (
            $review,
            $data,
            $type
        ) {
            $lockedReview = ProductReview::query()
                ->lockForUpdate()
                ->findOrFail($review->id);

            $media = $lockedReview->media()->create([
                'type' => $type,
                'disk' => trim($data['disk']),
                'file_path' => trim($data['file_path']),
                'thumbnail' => $data['thumbnail'] ?? null,
                'title' => $data['title'] ?? null,
                'alt_text' => $data['alt_text'] ?? null,
                'mime_type' => $data['mime_type'] ?? null,
                'file_size' => isset($data['file_size'])
                    ? (int) $data['file_size']
                    : null,
                'duration' => $data['duration'] ?? null,
                'sort_order' => isset($data['sort_order'])
                    ? (int) $data['sort_order']
                    : (
                        (int) $lockedReview->media()->max('sort_order')
                        + 1
                    ),
                'is_primary' => false,
                'status' => (bool) ($data['status'] ?? true),
            ]);

            if (
                ($data['is_primary'] ?? false)
                && $media->status
            ) {
                $this->setPrimaryWithinTransaction(
                    $lockedReview,
                    $media
                );
            }

            return $media->refresh();
        });
    }

    /**
     * Set an active media record as the primary media.
     */
    public function setPrimary(
        ProductReviewMedia $media
    ): ProductReviewMedia {
        return DB::transaction(function () use ($media) {
            $lockedMedia = ProductReviewMedia::query()
                ->lockForUpdate()
                ->findOrFail($media->id);

            $review = ProductReview::query()
                ->lockForUpdate()
                ->findOrFail($lockedMedia->product_review_id);

            if (! $lockedMedia->status) {
                throw new InvalidArgumentException(
                    'Inactive media cannot be primary.'
                );
            }

            $this->setPrimaryWithinTransaction(
                $review,
                $lockedMedia
            );

            return $lockedMedia->refresh();
        });
    }

    /**
     * Reorder all media records belonging to a review.
     *
     * Every media ID must be supplied exactly once.
     */
    public function reorder(
        ProductReview $review,
        array $orderedIds
    ): void {
        DB::transaction(function () use (
            $review,
            $orderedIds
        ) {
            $lockedReview = ProductReview::query()
                ->lockForUpdate()
                ->findOrFail($review->id);

            $mediaIds = $lockedReview->media()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            $requestedIds = collect($orderedIds)
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            if (
                count($requestedIds)
                    !== count(array_unique($requestedIds))
                || $requestedIds !== $mediaIds
            ) {
                throw new InvalidArgumentException(
                    'The order must contain every review media ID exactly once.'
                );
            }

            foreach (array_values($orderedIds) as $position => $id) {
                $lockedReview->media()
                    ->whereKey((int) $id)
                    ->update([
                        'sort_order' => $position,
                    ]);
            }
        });
    }

    /**
     * Activate or deactivate media.
     */
    public function setStatus(
        ProductReviewMedia $media,
        bool $active
    ): ProductReviewMedia {
        return DB::transaction(function () use (
            $media,
            $active
        ) {
            $lockedMedia = ProductReviewMedia::query()
                ->lockForUpdate()
                ->findOrFail($media->id);

            ProductReview::query()
                ->lockForUpdate()
                ->findOrFail($lockedMedia->product_review_id);

            $lockedMedia->update([
                'status' => $active,
                'is_primary' => $active
                    ? $lockedMedia->is_primary
                    : false,
            ]);

            return $lockedMedia->refresh();
        });
    }

    /**
     * Delete the database record.
     *
     * Physical file deletion is handled separately.
     */
    public function delete(
        ProductReviewMedia $media
    ): bool {
        return DB::transaction(function () use ($media) {
            $lockedMedia = ProductReviewMedia::query()
                ->lockForUpdate()
                ->findOrFail($media->id);

            return (bool) $lockedMedia->delete();
        });
    }

    /**
     * Ensure only one media record is primary for this review.
     */
    protected function setPrimaryWithinTransaction(
        ProductReview $review,
        ProductReviewMedia $media
    ): void {
        $review->media()->update([
            'is_primary' => false,
        ]);

        $media->update([
            'is_primary' => true,
        ]);
    }
}