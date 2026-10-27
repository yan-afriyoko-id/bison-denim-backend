<?php

namespace App\Repositories;

use App\Exceptions\CartStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartRepository
{
    public function getByUser()
    {
        $userId = Auth::id();
        return Cart::where('user_id', $userId)
            ->with(['items.variant', 'items.store'])
            ->firstOrCreate(['user_id' => $userId]);
    }

    public function addItem(array $data)
    {
        $cart = $this->getByUser();

        $existingItem = $cart->items()
            ->where('variant_id', $data['variant_id'])
            ->first();

        $qty = $data['qty'] ?? 1;
        $inCartQty = $existingItem->qty ?? 0;
        $storeId = $data['store_id'] ?? $existingItem?->store_id;

        $this->assertStockAvailable($data['variant_id'], $inCartQty + $qty, $storeId, $inCartQty);

        if ($existingItem) {
            $existingItem->qty += $qty;
            if (isset($data['note'])) {
                $existingItem->note = $data['note'];
            }
            if (isset($data['store_id'])) {
                $existingItem->store_id = $data['store_id'];
            }
            if (isset($data['is_protected'])) {
                $existingItem->is_protected = $data['is_protected'];
            }
            $existingItem->save();

            return $existingItem;
        }
        return $cart->items()->create($data);
    }

    public function updateItem($variantId, array $data)
    {
        $cart = $this->getByUser();
        $item = $cart->items()->where('variant_id', $variantId)->first();

        if (!$item) return null;

        if (isset($data['qty']) && $data['qty'] > $item->qty) {
            $this->assertStockAvailable($variantId, $data['qty'], $item->store_id, $item->qty);
        }

        $item->update($data);
        return $item;
    }

    public function removeItem($variantId)
    {
        $cart = $this->getByUser();
        return $cart->items()->where('variant_id', $variantId)->delete();
    }

    public function clear()
    {
        $cart = $this->getByUser();
        return $cart->items()->delete();
    }

    private function assertStockAvailable(int $variantId, int $totalQty, ?int $storeId, int $inCartQty): void
    {
        $variant = ProductVariant::with('stockRelations')->find($variantId);

        if (!$variant || $variant->status !== 'ACTIVE') {
            throw new CartStockException('Produk tidak tersedia.', 0, $inCartQty);
        }

        if ($variant->is_ignore_stock) {
            return;
        }

        if ($storeId) {
            $storeStock = $variant->stockRelations->firstWhere('store_id', $storeId);
            $available = $storeStock ? max(0, $storeStock->qty - $storeStock->reserved_qty) : 0;
        } else {
            $available = (int) $variant->stock;
        }

        if ($totalQty <= $available) {
            return;
        }

        if ($available <= 0) {
            $message = 'Stok produk habis.';
        } elseif ($inCartQty > 0) {
            $message = "Stok tersisa {$available}, di keranjang sudah ada {$inCartQty}.";
        } else {
            $message = "Stok tersisa {$available}.";
        }

        throw new CartStockException($message, $available, $inCartQty);
    }
}
