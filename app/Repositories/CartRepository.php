<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
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

        if ($existingItem) {
            $existingItem->qty += $data['qty'] ?? 1;
            $existingItem->save();
            if (isset($data['note'])) {
                $existingItem->note = $data['note'];
            }
            if (isset($data['store_id'])) {
                $existingItem->store_id = $data['store_id'];
            }
            if (isset($data['is_protected'])) {
                $existingItem->is_protected = $data['is_protected'];
            }

            return $existingItem;
        }
        return $cart->items()->create($data);
    }

    public function updateItem($variantId, array $data)
    {
        $cart = $this->getByUser();
        $item = $cart->items()->where('variant_id', $variantId)->first();

        if (!$item) return null;

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
}
