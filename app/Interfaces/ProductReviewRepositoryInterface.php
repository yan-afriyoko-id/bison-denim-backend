<?php

namespace App\Interfaces;

use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Collection;

interface ProductReviewRepositoryInterface
{
    public function getAllForProduct(int $productId): Collection;
    public function getAllByUser(int $userId): Collection;
    public function create(array $data): ProductReview;
    public function existsForUserAndProduct(int $userId, int $productId): bool;
    public function findById(int $id): ?ProductReview;
    public function update(int $id, array $data): ProductReview;
}