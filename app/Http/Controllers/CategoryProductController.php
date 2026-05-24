<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryProductController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(): JsonResponse
    {
        try {
            $categories = ProductCategory::with(['fk_product', 'fk_category'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Product categories retrieved successfully',
                'data' => $categories,
                'total' => $categories->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories by product ID.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            $categories = ProductCategory::where('fk_product_id', $productId)
                ->with('fk_category')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Product categories retrieved successfully',
                'data' => [
                    'categories' => $categories,
                ],
                'total' => $categories->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product category.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'fk_category_id' => 'required|integer|exists:taxo_lists,id',
            ]);

            // Check if already exists
            $exists = ProductCategory::where('fk_product_id', $validated['fk_product_id'])
                ->where('fk_category_id', $validated['fk_category_id'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product-category relationship already exists',
                ], 409);
            }

            $category = ProductCategory::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product category created successfully',
                'data' => $category->load(['fk_product', 'fk_category']),
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
                'message' => 'Error creating product category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product category.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = ProductCategory::with(['fk_product', 'fk_category'])->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product category not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product category retrieved successfully',
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $category = ProductCategory::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product category not found',
                ], 404);
            }

            $validated = $request->validate([
                'fk_product_id' => 'sometimes|required|integer|exists:products,id',
                'fk_category_id' => 'sometimes|required|integer|exists:taxo_lists,id',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product category updated successfully',
                'data' => $category->load(['fk_product', 'fk_category']),
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
                'message' => 'Error updating product category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product category.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = ProductCategory::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product category not found',
                ], 404);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Attach multiple categories to a product.
     */
    public function attachCategories(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer|exists:taxo_lists,id',
            ]);

            $attached = [];
            foreach ($validated['category_ids'] as $categoryId) {
                $exists = ProductCategory::where('fk_product_id', $validated['fk_product_id'])
                    ->where('fk_category_id', $categoryId)
                    ->exists();

                if (!$exists) {
                    $cat = ProductCategory::create([
                        'fk_product_id' => $validated['fk_product_id'],
                        'fk_category_id' => $categoryId,
                    ]);
                    $attached[] = $cat;
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($attached) . ' categories attached successfully',
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
                'message' => 'Error attaching categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detach categories from a product.
     */
    public function detachCategories(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer',
            ]);

            $deleted = ProductCategory::where('fk_product_id', $validated['fk_product_id'])
                ->whereIn('fk_category_id', $validated['category_ids'])
                ->delete();

        return response()->json([
            'success' => true,
                'message' => $deleted . ' categories detached successfully',
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
                'message' => 'Error detaching categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

