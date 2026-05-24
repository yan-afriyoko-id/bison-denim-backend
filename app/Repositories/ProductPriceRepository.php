<?php

namespace App\Repositories;

use App\Models\ProductPrice;

class ProductPriceRepository
{
    /**
     * Create a new product price.
     */
    public function create(array $data): ProductPrice
    {
        return ProductPrice::create($data);
    }

    /**
     * Find a product price by ID.
     */
    public function findById(int $id): ?ProductPrice
    {
        return ProductPrice::with('fk_product')->find($id);
    }

    /**
     * Get all product prices.
     */
    public function all()
    {
        return ProductPrice::with('fk_product')
            ->orderBy('start_qty', 'asc')
            ->get();
    }

    /**
     * Get prices by product ID.
     */
    public function getByProductId(int $productId)
    {
        return ProductPrice::where('fk_product_id', $productId)
            ->orderBy('start_qty', 'asc')
            ->get();
    }

    /**
     * Get price by product ID and quantity.
     */
    public function getPriceByQuantity(int $productId, int $quantity)
    {
        return ProductPrice::where('fk_product_id', $productId)
            ->where('start_qty', '<=', $quantity)
            ->orderBy('start_qty', 'desc')
            ->first();
    }

    /**
     * Update a product price.
     */
    public function update(int $id, array $data): ?ProductPrice
    {
        $price = $this->findById($id);

        if (!$price) {
            return null;
        }

        $price->update($data);

        return $price->fresh('fk_product');
    }

    /**
     * Delete a product price.
     */
    public function delete(int $id): bool
    {
        $price = $this->findById($id);

        if (!$price) {
            return false;
        }

        return $price->delete();
    }

    /**
     * Delete prices by product ID.
     */
    public function deleteByProductId(int $productId): bool
    {
        return ProductPrice::where('fk_product_id', $productId)->delete();
    }
}
