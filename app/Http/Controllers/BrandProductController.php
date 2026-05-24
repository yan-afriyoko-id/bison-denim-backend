<?php

namespace App\Http\Controllers;

use App\Models\BrandProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BrandProductController extends Controller
{
    /**
     * Display a listing of brand products.
     */
    public function index(): JsonResponse
    {
        try {
            $brandProducts = BrandProduct::with(['fk_product', 'fk_brand'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Brand products retrieved successfully',
                'data' => $brandProducts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving brand products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get brand products by product ID.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            $brandProducts = BrandProduct::where('fk_product_id', $productId)
                ->with('fk_brand')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Brand products retrieved successfully',
                'data' => [
                    'brand_products' => $brandProducts,
                ],
                'total' => $brandProducts->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving brand products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created brand-product relationship.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'fk_brand_id' => 'required|integer|exists:brands,id',
            ]);

            // Check if the relationship already exists
            $exists = BrandProduct::where('fk_product_id', $validated['fk_product_id'])
                ->where('fk_brand_id', $validated['fk_brand_id'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This brand-product relationship already exists',
                ], 409);
            }

            $brandProduct = BrandProduct::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Brand-product relationship created successfully',
                'data' => $brandProduct->load(['fk_product', 'fk_brand']),
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
                'message' => 'Error creating brand-product relationship',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Attach multiple brands to a product.
     */
    public function attachBrands(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'brand_ids' => 'required|array',
                'brand_ids.*' => 'integer|exists:brands,id',
            ]);

            $attached = [];
            foreach ($validated['brand_ids'] as $brandId) {
                $exists = BrandProduct::where('fk_product_id', $validated['fk_product_id'])
                    ->where('fk_brand_id', $brandId)
                    ->exists();

                if (!$exists) {
                    $brandProduct = BrandProduct::create([
                        'fk_product_id' => $validated['fk_product_id'],
                        'fk_brand_id' => $brandId,
                    ]);
                    $attached[] = $brandProduct;
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($attached) . ' brands attached successfully',
                'data' => $attached,
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
                'message' => 'Error attaching brands',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detach brands from a product.
     */
    public function detachBrands(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'brand_ids' => 'required|array',
                'brand_ids.*' => 'integer',
            ]);

            $deleted = BrandProduct::where('fk_product_id', $validated['fk_product_id'])
                ->whereIn('fk_brand_id', $validated['brand_ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted . ' brands detached successfully',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error detaching brands',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified brand-product relationship.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $brandProduct = BrandProduct::find($id);

            if (!$brandProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand-product relationship not found',
                ], 404);
            }

            $brandProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Brand-product relationship deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting brand-product relationship',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
