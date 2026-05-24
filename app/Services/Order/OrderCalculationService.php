<?php

namespace App\Services\Order;

use App\Services\Cart\CartCalculationService;

class OrderCalculationService
{
    protected CartCalculationService $cartCalculationService;

    public function __construct(CartCalculationService $cartCalculationService)
    {
        $this->cartCalculationService = $cartCalculationService;
    }

    /**
     * Calculate order totals including shipping and discounts
     * 
     * @param array $cartData
     * @param int $shippingCost
     * @param int|null $voucherDiscount
     * @return array
     */
    public function calculateOrder(array $cartData, int $shippingCost = 0, ?int $voucherDiscount = null): array
    {
        // Calculate cart first
        $cartResult = $this->cartCalculationService->calculateCart($cartData);
        
        $subtotal = $cartResult['calculation']['sub_total'];
        $discountAmount = $voucherDiscount ?? 0;
        $totalAmount = $subtotal + $shippingCost - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_cost' => $shippingCost,
            'total_amount' => max(0, $totalAmount), // Ensure total is not negative
            'cart' => $cartResult['cart'],
            'out_of_stock' => $cartResult['out_of_stock'],
        ];
    }
}


