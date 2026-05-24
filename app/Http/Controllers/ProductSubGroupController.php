<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource\ProductResource;
use App\Models\ProductSubGroup;
use App\Models\ProductGroup;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductSubGroupController extends Controller
{
    /**
     * Get all sub-groups for a product group
     */
    public function index(Request $request, $groupId)
    {
        try {
            $query = ProductSubGroup::where('product_group_id', $groupId)
                ->with(['products' => function ($query) use ($request) {
                    if ($request->has('limit') && is_numeric($request->limit)) {
                        $query->take((int) $request->limit);
                    }

                    $query->with([
                        'hasMany_image',
                        'hasMany_variant',
                        'hasMany_category',
                        'reviews',
                    ])->orderByPivot('sort');
                }]);

            $subGroups = $query->get();

            return response()->json([
                'success' => true,
                'data' => $subGroups->map(function ($subGroup) {
                    return [
                        'id' => $subGroup->id,
                        'product_group_id' => $subGroup->product_group_id,
                        'title' => $subGroup->title,
                        'sort' => $subGroup->sort,
                        'status' => $subGroup->status,
                        'created_at' => $subGroup->created_at,
                        'updated_at' => $subGroup->updated_at,
                        'products' => ProductResource::collection($subGroup->products),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sub-groups',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new sub-group
     */
    public function store(Request $request, $groupId)
    {
        ProductGroup::findOrFail($groupId);

        $validated = $request->validate([
            'title' => 'required|string',
            'sort' => 'nullable|integer|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        try {
            $subGroup = ProductSubGroup::create([
                'product_group_id' => $groupId,
                ...$validated,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub-group created successfully',
                'data' => $subGroup,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sub-group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single sub-group with products
     */
    public function show(Request $request, $groupId, $subGroupId)
    {
        try {
            $query = ProductSubGroup::where('product_group_id', $groupId)
                ->with(['products' => function ($query) use ($request) {
                    if ($request->has('limit') && is_numeric($request->limit)) {
                        $query->take((int) $request->limit);
                    }

                    $query->with([
                        'hasMany_image',
                        'hasMany_variant',
                        'hasMany_category',
                        'reviews',
                    ])->orderByPivot('sort');
                }]);

            $subGroup = $query->findOrFail($subGroupId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $subGroup->id,
                    'product_group_id' => $subGroup->product_group_id,
                    'title' => $subGroup->title,
                    'sort' => $subGroup->sort,
                    'status' => $subGroup->status,
                    'created_at' => $subGroup->created_at,
                    'updated_at' => $subGroup->updated_at,
                    'products' => ProductResource::collection($subGroup->products),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-group not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update a sub-group
     */
    public function update(Request $request, $groupId, $subGroupId)
    {
        $subGroup = ProductSubGroup::where('product_group_id', $groupId)
            ->findOrFail($subGroupId);

        $validated = $request->validate([
            'title' => 'required|string',
            'sort' => 'nullable|integer|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        try {
            $subGroup->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sub-group updated successfully',
                'data' => $subGroup,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sub-group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a sub-group
     */
    public function destroy($groupId, $subGroupId)
    {
        try {
            $subGroup = ProductSubGroup::where('product_group_id', $groupId)
                ->findOrFail($subGroupId);

            $subGroup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sub-group deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub-group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add products to a sub-group
     */
    public function addProducts(Request $request, $groupId, $subGroupId)
    {
        $subGroup = ProductSubGroup::where('product_group_id', $groupId)
            ->findOrFail($subGroupId);

        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        try {
            // Sync products (attach new ones, keep existing)
            $subGroup->products()->syncWithoutDetaching($validated['product_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Products added to sub-group successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a product from a sub-group
     */
    public function removeProduct($groupId, $subGroupId, $productId)
    {
        try {
            $subGroup = ProductSubGroup::where('product_group_id', $groupId)
                ->findOrFail($subGroupId);

            $subGroup->products()->detach($productId);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from sub-group successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
