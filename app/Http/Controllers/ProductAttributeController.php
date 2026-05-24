<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductAttributeController extends Controller
{
    /**
     * Get attributes for a product.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $productAttributes = ProductAttribute::with(['attribute.attributeValues', 'attributeValues.attributeValue'])
                ->where('product_id', $productId)
                ->orderBy('sort', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Product attributes retrieved successfully',
                'data' => $productAttributes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product attributes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Attach attributes to a product.
     */
    public function attachAttributes(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $validated = $request->validate([
                'attributes' => 'required|array',
                'attributes.*.attribute_id' => 'required|integer|exists:attributes,id',
                'attributes.*.sort' => 'nullable|integer|min:0',
                'attributes.*.values' => 'required|array',
                'attributes.*.values.*' => 'required|integer|exists:attribute_values,id',
            ]);

            foreach ($validated['attributes'] as $attrData) {
                // Create or update product_attribute
                $productAttribute = ProductAttribute::updateOrCreate(
                    [
                        'product_id' => $productId,
                        'attribute_id' => $attrData['attribute_id'],
                    ],
                    [
                        'sort' => $attrData['sort'] ?? 0,
                    ]
                );

                // Sync attribute values
                $attributeValueIds = $attrData['values'];
                $existingValueIds = $productAttribute->attributeValues()
                    ->pluck('attribute_value_id')
                    ->toArray();

                // Add new values
                $newValueIds = array_diff($attributeValueIds, $existingValueIds);
                foreach ($newValueIds as $valueId) {
                    ProductAttributeValue::create([
                        'product_attribute_id' => $productAttribute->id,
                        'attribute_value_id' => $valueId,
                    ]);
                }

                // Remove values that are no longer selected
                $removedValueIds = array_diff($existingValueIds, $attributeValueIds);
                ProductAttributeValue::where('product_attribute_id', $productAttribute->id)
                    ->whereIn('attribute_value_id', $removedValueIds)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Attributes attached successfully',
                'data' => $this->getByProduct($productId)->getData()->data,
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
                'message' => 'Error attaching attributes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detach an attribute from a product.
     */
    public function detachAttribute(int $productId, int $attributeId): JsonResponse
    {
        try {
            $productAttribute = ProductAttribute::where('product_id', $productId)
                ->where('attribute_id', $attributeId)
                ->first();

            if (!$productAttribute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product attribute not found',
                ], 404);
            }

            $productAttribute->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attribute detached successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error detaching attribute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

