<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource\CartResource;
use App\Repositories\CartRepository;
use App\Services\Cart\CartCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartRepository $cartRepository;
    protected CartCalculationService $cartCalculationService;

    public function __construct(
        CartRepository $cartRepository,
        CartCalculationService $cartCalculationService
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartCalculationService = $cartCalculationService;
    }

    /**
     * Get current user's cart
     */
    public function index(): JsonResponse
    {
        try {
            $result = $this->cartCalculationService->calculateCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart retrieved successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'variant_id' => 'required|exists:product_variants,id',
                'qty' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
                'store_id' => 'nullable|exists:stores,id',
                'is_protected' => 'boolean',
            ]);

            $item = $this->cartRepository->addItem($validated);

            $cart = $this->cartRepository->getByUser();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'data' => new CartResource($cart),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item qty/note
     */
    public function update(Request $request, int $variantId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'qty' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            $updated = $this->cartRepository->updateItem($variantId, $validated);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart',
                ], 404);
            }

            $cart = $this->cartRepository->getByUser();

            return response()->json([
                'success' => true,
                'message' => 'Item updated',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(int $variantId): JsonResponse
    {
        try {
            $deleted = $this->cartRepository->removeItem($variantId);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart',
                ], 404);
            }

            $cart = $this->cartRepository->getByUser();

            return response()->json([
                'success' => true,
                'message' => 'Item removed',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartRepository->clear();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
