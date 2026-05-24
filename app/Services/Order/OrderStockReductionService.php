<?php

namespace App\Services\Order;

use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use Illuminate\Support\Facades\DB;

class OrderStockReductionService
{
    public function reserveStock(array $cartItems): void
    {
        DB::transaction(function () use ($cartItems) {
            foreach ($cartItems as $item) {
                $variantId = $item['variant_id'];
                $qty = $item['qty'];
                $storeId = $item['store']['id'] ?? $item['store_id'] ?? null;

                $variant = ProductVariant::find($variantId);
                
                if (!$variant || $variant->is_ignore_stock) {
                    continue;
                }

                $stockQuery = ProductVariantStock::where('variant_id', $variantId);
                
                if ($storeId) {
                    $stockQuery->where('store_id', $storeId);
                }
                
                $stockRecords = $stockQuery
                    ->orderBy('qty', 'desc')
                    ->lockForUpdate()
                    ->get();

                $totalAvailableStock = $stockRecords->sum(function ($record) {
                    return max(0, $record->qty - $record->reserved_qty);
                });

                if ($totalAvailableStock < $qty) {
                    $storeInfo = $storeId ? " for store {$storeId}" : "";
                    throw new \Exception("Insufficient stock for variant {$variantId}{$storeInfo}. Available: {$totalAvailableStock}, Requested: {$qty}");
                }

                $remainingQty = $qty;

                foreach ($stockRecords as $stockRecord) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    $availableQty = max(0, $stockRecord->qty - $stockRecord->reserved_qty);
                    
                    if ($availableQty > 0) {
                        $reserveQty = min($remainingQty, $availableQty);
                        $stockRecord->reserved_qty += $reserveQty;
                        $stockRecord->save();
                        $remainingQty -= $reserveQty;
                    }
                }
            }
        });
    }

    public function convertReservedToActual(array $orderItems): void
    {
        DB::transaction(function () use ($orderItems) {
            foreach ($orderItems as $item) {
                $variantId = $item['variant_id'] ?? $item['fk_variant_id'] ?? null;
                $qty = $item['qty'] ?? 0;
                $storeId = $item['store_id'] ?? null;

                if (!$variantId || $qty <= 0) {
                    continue;
                }

                $variant = ProductVariant::find($variantId);
                
                if (!$variant || $variant->is_ignore_stock) {
                    continue;
                }

                $stockQuery = ProductVariantStock::where('variant_id', $variantId)
                    ->where('reserved_qty', '>', 0);
                
                if ($storeId) {
                    $stockQuery->where('store_id', $storeId);
                }
                
                $stockRecords = $stockQuery
                    ->orderBy('reserved_qty', 'desc')
                    ->lockForUpdate()
                    ->get();

                $remainingQty = $qty;

                foreach ($stockRecords as $stockRecord) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($stockRecord->reserved_qty > 0) {
                        $convertQty = min($remainingQty, $stockRecord->reserved_qty);
                        
                        $stockRecord->reserved_qty -= $convertQty;
                        $stockRecord->qty -= $convertQty;
                        $stockRecord->save();
                        
                        $remainingQty -= $convertQty;
                    }
                }
            }
        });
    }

    public function releaseReservedStock(array $orderItems): void
    {
        DB::transaction(function () use ($orderItems) {
            foreach ($orderItems as $item) {
                $variantId = $item['variant_id'] ?? $item['fk_variant_id'] ?? null;
                $qty = $item['qty'] ?? 0;
                $storeId = $item['store_id'] ?? null;

                if (!$variantId || $qty <= 0) {
                    continue;
                }

                $variant = ProductVariant::find($variantId);
                
                if (!$variant || $variant->is_ignore_stock) {
                    continue;
                }

                $stockQuery = ProductVariantStock::where('variant_id', $variantId)
                    ->where('reserved_qty', '>', 0);
                
                if ($storeId) {
                    $stockQuery->where('store_id', $storeId);
                }
                
                $stockRecords = $stockQuery
                    ->orderBy('reserved_qty', 'desc')
                    ->lockForUpdate()
                    ->get();

                $remainingQty = $qty;

                foreach ($stockRecords as $stockRecord) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($stockRecord->reserved_qty > 0) {
                        $releaseQty = min($remainingQty, $stockRecord->reserved_qty);
                        $stockRecord->reserved_qty -= $releaseQty;
                        $stockRecord->save();
                        $remainingQty -= $releaseQty;
                    }
                }
            }
        });
    }

    public function reduceStock(array $cartItems): void
    {
        $this->reserveStock($cartItems);
    }

    public function restoreStock(array $orderItems): void
    {
        $this->releaseReservedStock($orderItems);
    }
}


