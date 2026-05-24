<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository
{
    /**
     * Create a new product image.
     */
    public function create(array $data): ProductImage
    {
        return ProductImage::create($data);
    }

    /**
     * Find a product image by ID.
     */
    public function findById(int $id): ?ProductImage
    {
        return ProductImage::with('fk_product')->find($id);
    }

    /**
     * Get all product images.
     */
    public function all()
    {
        return ProductImage::with('fk_product')
            ->orderBy('order_number', 'asc')
            ->get();
    }

    /**
     * Get images by product ID.
     */
    public function getByProductId(int $productId)
    {
        return ProductImage::where('fk_product_id', $productId)
            ->orderBy('order_number', 'asc')
            ->get();
    }

    /**
     * Get primary image by product ID.
     */
    public function getPrimaryByProductId(int $productId)
    {
        return ProductImage::where('fk_product_id', $productId)
            ->orderBy('order_number', 'asc')
            ->first();
    }

    /**
     * Update a product image.
     */
    public function update(int $id, array $data): ?ProductImage
    {
        $image = $this->findById($id);

        if (!$image) {
            return null;
        }

        $image->update($data);

        return $image->fresh('fk_product');
    }

    /**
     * Delete a product image.
     */
    public function delete(int $id): bool
    {
        $image = $this->findById($id);

        if (!$image) {
            return false;
        }

        return $image->delete();
    }

    /**
     * Delete images by product ID.
     */
    public function deleteByProductId(int $productId): bool
    {
        return ProductImage::where('fk_product_id', $productId)->delete();
    }
}
