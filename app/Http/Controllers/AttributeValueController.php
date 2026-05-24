<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of attribute values.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AttributeValue::with('attribute');

            if ($request->has('attribute_id')) {
                $query->where('attribute_id', $request->attribute_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $attributeValues = $query->orderBy('sort', 'asc')
                ->orderBy('value', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Attribute values retrieved successfully',
                'data' => $attributeValues,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attribute values',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get attribute values by attribute ID.
     */
    public function getByAttribute(int $attributeId): JsonResponse
    {
        try {
            $attributeValues = AttributeValue::where('attribute_id', $attributeId)
                ->where('status', 'ACTIVE')
                ->orderBy('sort', 'asc')
                ->orderBy('value', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Attribute values retrieved successfully',
                'data' => $attributeValues,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attribute values',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created attribute value.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'attribute_id' => 'required|integer|exists:attributes,id',
                'value' => 'required|string|max:100',
                'sort' => 'nullable|integer|min:0',
                'status' => 'nullable|in:ACTIVE,INACTIVE',
            ]);

            // Check unique value per attribute
            $exists = AttributeValue::where('attribute_id', $validated['attribute_id'])
                ->where('slug', Str::slug($validated['value']))
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute value already exists for this attribute',
                    'errors' => ['value' => ['This value already exists for the selected attribute']],
                ], 422);
            }

            $validated['slug'] = Str::slug($validated['value']);
            $validated['status'] = $validated['status'] ?? 'ACTIVE';
            $validated['sort'] = $validated['sort'] ?? 0;

            $attributeValue = AttributeValue::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value created successfully',
                'data' => $attributeValue->load('attribute'),
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
                'message' => 'Error creating attribute value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified attribute value.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $attributeValue = AttributeValue::with('attribute')->find($id);

            if (!$attributeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute value not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attribute value retrieved successfully',
                'data' => $attributeValue,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attribute value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified attribute value.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $attributeValue = AttributeValue::find($id);

            if (!$attributeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute value not found',
                ], 404);
            }

            $validated = $request->validate([
                'value' => 'required|string|max:100',
                'sort' => 'nullable|integer|min:0',
                'status' => 'nullable|in:ACTIVE,INACTIVE',
            ]);

            // Check unique value per attribute (excluding current)
            $exists = AttributeValue::where('attribute_id', $attributeValue->attribute_id)
                ->where('slug', Str::slug($validated['value']))
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute value already exists for this attribute',
                    'errors' => ['value' => ['This value already exists for the selected attribute']],
                ], 422);
            }

            $validated['slug'] = Str::slug($validated['value']);

            $attributeValue->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value updated successfully',
                'data' => $attributeValue->fresh(['attribute']),
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
                'message' => 'Error updating attribute value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified attribute value.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $attributeValue = AttributeValue::find($id);

            if (!$attributeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute value not found',
                ], 404);
            }

            $attributeValue->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attribute value deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attribute value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

