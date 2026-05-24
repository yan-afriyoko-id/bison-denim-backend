<?php

namespace App\Repositories;

use App\Interfaces\ProductReviewRepositoryInterface;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Collection;

class ProductReviewRepository implements ProductReviewRepositoryInterface
{
    public function getAllForProduct(int $productId): Collection
    {
        return ProductReview::approved()
            ->where('fk_product_id', $productId)
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    public function getAllByUser(int $userId): Collection
    {
        return ProductReview::where('user_id', $userId)
            ->with('product:id,title')
            ->latest()
            ->get();
    }

    public function create(array $data): ProductReview
    {
        return ProductReview::create($data);
    }

    public function existsForUserAndProduct(int $userId, int $productId): bool
    {
        return ProductReview::where('user_id', $userId)
            ->where('fk_product_id', $productId)
            ->exists();
    }

    public function findById(int $id): ?ProductReview
    {
        return ProductReview::find($id);
    }

    public function update(int $id, array $data): ProductReview
    {
        $review = ProductReview::findOrFail($id);
        $review->update($data);
        return $review->fresh();
    }
}