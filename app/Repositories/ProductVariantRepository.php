<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\ProductVariantStock;
use App\Models\AttributeValue;
use App\Models\Store;

class ProductVariantRepository
{
    /**
     * Create a new product variant.
     */
    public function create(array $data, array $attributeValueIds = []): ProductVariant
    {
        // Extract stock from data if provided
        $stock = null;
        if (isset($data['stock'])) {
            $stock = $data['stock'];
            unset($data['stock']); // Remove stock from variant data
        }

        $variant = ProductVariant::create($data);

        // Create product_variant_options if attribute_value_ids provided
        if (!empty($attributeValueIds)) {
            $this->syncVariantOptions($variant->id, $attributeValueIds);
        }

        // Create stock record if stock is provided
        if ($stock !== null) {
            $this->syncVariantStock($variant->id, $stock);
        }

        return $variant->load(['options.attribute', 'options.attributeValue', 'stockRelations.store']);
    }

    /**
     * Sync variant options (attribute values).
     */
    protected function syncVariantOptions(int $variantId, array $attributeValueIds): void
    {
        // Delete existing options
        ProductVariantOption::where('variant_id', $variantId)->delete();

        // Get attribute values with their attributes
        $attributeValues = AttributeValue::whereIn('id', $attributeValueIds)
            ->with('attribute')
            ->get();

        // Create new options
        foreach ($attributeValues as $attributeValue) {
            ProductVariantOption::create([
                'variant_id' => $variantId,
                'attribute_id' => $attributeValue->attribute_id,
                'attribute_value_id' => $attributeValue->id,
            ]);
        }
    }

    /**
     * Sync variant stock (create or update stock record).
     * Note: This method now requires store_id. If you need to sync stock for a specific store,
     * you should call this with store_id parameter or use a different method.
     * 
     * For backward compatibility, if no store_id is provided, this will sum all stocks
     * from all stores. However, it's recommended to manage stock per store explicitly.
     */
    protected function syncVariantStock(int $variantId, ?int $stock, ?int $storeId = null): void
    {
        // If stock is null, set to 0
        $stock = $stock ?? 0;

        // If store_id is provided, update/create stock for that specific store
        if ($storeId !== null) {
            ProductVariantStock::updateOrCreate(
                [
                    'variant_id' => $variantId,
                    'store_id' => $storeId,
                ],
                [
                    'qty' => $stock,
                    'reserved_qty' => 0, // Default reserved quantity
                ]
            );
        } else {
            // For backward compatibility: if no store_id, we'll try to find the first store
            // or create a default stock record. However, this is not recommended.
            // It's better to always specify store_id.
            $firstStore = Store::where('status', 'ACTIVE')->first();
            if ($firstStore) {
                ProductVariantStock::updateOrCreate(
                    [
                        'variant_id' => $variantId,
                        'store_id' => $firstStore->id,
                    ],
                    [
                        'qty' => $stock,
                        'reserved_qty' => 0,
                    ]
                );
            }
        }
    }

    /**
     * Find a product variant by ID.
     */
    public function findById(int $id): ?ProductVariant
    {
        return ProductVariant::with(['product', 'options.attribute', 'options.attributeValue', 'stockRelations.store'])->find($id);
    }

    /**
     * Get all product variants.
     */
    public function all()
    {
        return ProductVariant::with(['product', 'options.attribute', 'options.attributeValue', 'stockRelations.store'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get variants by product ID.
     */
    public function getByProductId(int $productId)
    {
        return ProductVariant::where('fk_product_id', $productId)
            ->with(['options.attribute', 'options.attributeValue', 'stockRelations.store'])
            ->orderBy('price', 'asc')
            ->get();
    }

    /**
     * Get active variants by product ID.
     */
    public function getActiveByProductId(int $productId)
    {
        return ProductVariant::where('fk_product_id', $productId)
            ->where('status', 'ACTIVE')
            ->with(['options.attribute', 'options.attributeValue', 'stockRelations.store'])
            ->orderBy('price', 'asc')
            ->get();
    }

    /**
     * Get cheapest variant by product ID.
     */
    public function getCheapestByProductId(int $productId)
    {
        return ProductVariant::where('fk_product_id', $productId)
            ->with(['options.attribute', 'options.attributeValue', 'stockRelations.store'])
            ->orderBy('price', 'asc')
            ->first();
    }

    /**
     * Update a product variant.
     */
    public function update(int $id, array $data, ?array $attributeValueIds = null): ?ProductVariant
    {
        $variant = $this->findById($id);

        if (!$variant) {
            return null;
        }

        // Extract stock from data if provided
        $stock = null;
        if (isset($data['stock'])) {
            $stock = $data['stock'];
            unset($data['stock']); // Remove stock from variant data
        }

        $variant->update($data);

        // Sync variant options if provided
        if ($attributeValueIds !== null) {
            $this->syncVariantOptions($variant->id, $attributeValueIds);
        }

        // Update stock if provided
        if ($stock !== null) {
            $this->syncVariantStock($variant->id, $stock);
        }

        return $variant->fresh(['options.attribute', 'options.attributeValue', 'stockRelations.store']);
    }

    /**
     * Delete a product variant.
     */
    public function delete(int $id): bool
    {
        $variant = $this->findById($id);

        if (!$variant) {
            return false;
        }

        return $variant->delete();
    }

    /**
     * Update stock for a variant.
     */
    public function updateStock(int $id, int $quantity): bool
    {
        $this->syncVariantStock($id, $quantity);
        return true;
    }

    /**
     * Check stock availability.
     */
    public function checkStock(int $id, int $quantity): bool
    {
        $variant = $this->findById($id);

        if (!$variant) {
            return false;
        }

        if ($variant->is_ignore_stock) {
            return true;
        }

        // Get stock using accessor (from product_variant_stocks table)
        $availableStock = $variant->stock ?? 0;

        return $availableStock >= $quantity;
    }
}
