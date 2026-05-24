<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Repositories\ConfigRepository;
use Illuminate\Support\Facades\Auth;

class CartCalculationService
{

    protected ConfigRepository $configRepository;
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }
    /**
     * Calculate cart items with stock checking and grouping
     * 
     * @param array $cartData Array of cart items: [['variant_id' => int, 'qty' => int, 'note' => string|null, 'store_id' => int|null], ...]
     * @return array
     */
    public function calculateCart(?array $cartData = null): array
    {
        $cartItems = [];

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)
                ->with(['items.variant.product.hasMany_image_getPrimaryImage', 'items.variant.stockRelations.store'])
                ->first();

            if ($cart) {
                $cartItems = $cart->items->map(function ($item) {
                    return [
                        'variant_id' => $item->variant_id,
                        'qty' => $item->qty,
                        'note' => $item->note,
                        'store_id' => $item->store_id,
                        'is_protected' => $item->is_protected,
                    ];
                })->toArray();
            }
        }

        if (empty($cartItems) && !empty($cartData)) {
            $cartItems = $cartData;
        }

        if (empty($cartItems)) {
            return [
                'cart' => [],
                'out_of_stock' => [],
                'calculation' => [
                    'sub_total' => 0,
                    'total_cart' => 0,
                    'total_weight' => 0,
                    'product_protection_percent' => 0,
                    'product_protection_amount' => 0,
                ],
            ];
        }

        $cart = [];
        $outOfStock = [];
        $subTotal = 0;
        $totalCart = 0;
        $totalWeight = 0;
        $totalProtection = 0;

        $variantIds = collect($cartItems)->pluck('variant_id')->unique();

        $variants = ProductVariant::withTrashed()
            ->with([
                'product.hasMany_image_getPrimaryImage',
                'product.hasMany_image',
                'stockRelations.store'
            ])
            ->whereIn('id', $variantIds)
            ->where('status', 'ACTIVE')
            ->get()
            ->keyBy('id');

        $config = $this->configRepository->getByKey('product_protection');
        $protectionPercent = (int) ($config->value ?? 0);

        foreach ($cartItems as $item) {
            $variantId = $item['variant_id'];
            $qty = $item['qty'];
            $note = $item['note'] ?? null;
            $requestedStoreId = $item['store_id'] ?? null;
            $isProtected = $item['is_protected'] ?? false;

            $variant = $variants[$variantId] ?? null;

            if (!$variant || $variant->trashed() || $variant->status !== 'ACTIVE') {

                $outOfStock[] = [
                    'variant_id' => $variantId,
                    'product_name' => $variant->product->name ?? 'Product not available',
                    'variant_name' => $variant->variant_name ?? 'Unknown Variant',
                    'qty' => $qty,
                    'reason' => $variant && $variant->trashed()
                        ? 'Product has been removed'
                        : 'Product not active',
                ];

                continue;
            }

            $product = $variant->product;
            $isFreeShipping = $product && $product->is_freeshiping === 'ACTIVE';

            $selectedStore = null;

            if ($variant->stockRelations) {

                if ($requestedStoreId) {
                    $storeStock = $variant->stockRelations
                        ->firstWhere('store_id', $requestedStoreId);

                    if ($storeStock && $storeStock->store) {
                        $availableQty = max(0, $storeStock->qty - $storeStock->reserved_qty);
                        if ($availableQty >= $qty) {
                            $selectedStore = $storeStock->store;
                        }
                    }
                }

                if (!$selectedStore) {
                    $storeStock = $variant->stockRelations
                        ->filter(function ($stock) use ($qty) {
                            $availableQty = max(0, $stock->qty - $stock->reserved_qty);
                            return $stock->store && $availableQty >= $qty;
                        })
                        ->sortByDesc(function ($stock) {
                            return max(0, $stock->qty - $stock->reserved_qty);
                        })
                        ->first();

                    if ($storeStock && $storeStock->store) {
                        $selectedStore = $storeStock->store;
                    }
                }
            }

            $availableStock = 0;

            if ($variant->is_ignore_stock) {
                $availableStock = PHP_INT_MAX;
            } elseif ($selectedStore) {
                $storeStock = $variant->stockRelations
                    ->firstWhere('store_id', $selectedStore->id);

                $availableStock = $storeStock
                    ? max(0, $storeStock->qty - $storeStock->reserved_qty)
                    : 0;
            } else {
                $availableStock = $variant->stock ?? 0;
            }

            $isInStock = $availableStock >= $qty;

            $actualPrice = (int) ($variant->strike_price ?? $variant->price ?? 0);
            $discountPrice = (int) ($variant->price ?? 0);
            $purchasePrice = $discountPrice;
            $itemSubtotal = $purchasePrice * $qty;

            $productProtectionAmount = 0;
            if ($isProtected && $protectionPercent > 0) {
                $productProtectionAmount = (int) floor($itemSubtotal * ($protectionPercent / 100));
            }

            $itemWeight = 0;

            if ($variant->weight !== null && $variant->weight > 0) {
                $weight = (float) $variant->weight;
                $typeWeight = $variant->type_weight ?? 'GRAM';

                $itemWeight = $typeWeight === 'KG' ? $weight * 1000 : $weight;
            } elseif ($product && $product->weight !== null && $product->weight > 0) {
                $weight = (float) $product->weight;
                $typeWeight = $product->type_weight ?? 'GRAM';

                $itemWeight = $typeWeight === 'KG' ? $weight * 1000 : $weight;
            } else {
                $itemWeight = 1000;
            }

            $cartItem = [
                'variant_id' => $variant->id,
                'product_id' => $product->id ?? null,
                'product_slug' => $product->slug ?? null,
                'product_name' => $product->name ?? 'Unknown Product',
                'variant_name' => $variant->variant_name ?? 'Default',
                'variant_description' => $this->getVariantDescription($variant),
                'sku' => $variant->sku ?? '',
                'image' => $this->getProductImage($variant, $product),
                'qty' => $qty,
                'actual_price' => $actualPrice,
                'discount_price' => $discountPrice,
                'purchase_price' => $purchasePrice,
                'sub_total' => $itemSubtotal,
                'product_protection_percent' => $protectionPercent,
                'product_protection_amount' => $productProtectionAmount,
                'is_protected' => $isProtected,
                'weight' => $itemWeight,
                'is_freeshiping' => $isFreeShipping ? 'ACTIVE' : 'INACTIVE',
                'note' => $note,
                'in_stock' => $isInStock,
                'available_stock' => $availableStock,
                'store' => $selectedStore ? [
                    'id' => $selectedStore->id,
                    'name' => $selectedStore->name,
                    'city_id' => $selectedStore->city_id,
                    'city' => $selectedStore->city,
                    'province' => $selectedStore->province,
                ] : null,
            ];

            if ($isInStock) {
                $cart[] = $cartItem;
                $subTotal += $itemSubtotal;
                $totalCart += $qty;
                $totalWeight += $itemWeight;
                $totalProtection += $productProtectionAmount;
            } else {
                $outOfStock[] = $cartItem;
            }
        }

        return [
            'cart' => $cart,
            'out_of_stock' => $outOfStock,
            'calculation' => [
                'sub_total' => $subTotal,
                'total_cart' => $totalCart,
                'total_weight' => $totalWeight,
                'product_protection_percent' => $protectionPercent,
                'product_protection_amount' => $totalProtection,
            ],
        ];
    }

    /**
     * Get variant description from variant options
     */
    private function getVariantDescription(ProductVariant $variant): string
    {
        if ($variant->variant_name) {
            return $variant->variant_name;
        }

        // Try to build from variant options
        $variant->load('options.attributeValue.attribute');
        $descriptions = [];

        foreach ($variant->options as $option) {
            if ($option->attributeValue && $option->attributeValue->attribute) {
                $descriptions[] = $option->attributeValue->value;
            }
        }

        return implode(' - ', $descriptions) ?: 'Default';
    }

    /**
     * Get product image with fallback
     * Priority: variant image > product primary image > product first image > null
     */
    private function getProductImage($variant, $product): ?string
    {
        // 1. Try variant image first
        if ($variant && $variant->image_path) {
            return $this->getImageUrl($variant->image_path);
        }

        // 2. Try product primary/featured image
        if ($product) {
            // Try primary image (featured or first ordered)
            $primaryImage = $product->hasMany_image_getPrimaryImage;
            if ($primaryImage && $primaryImage->path) {
                return $this->getImageUrl($primaryImage->path);
            }

            // 3. Try first image from hasMany_image
            $productImages = $product->hasMany_image;
            if ($productImages && $productImages->isNotEmpty()) {
                $firstImage = $productImages->first();
                if ($firstImage && $firstImage->path) {
                    return $this->getImageUrl($firstImage->path);
                }
            }
        }

        // 4. No image found
        return null;
    }

    /**
     * Get full URL for image
     */
    private function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset($path);
    }
}
