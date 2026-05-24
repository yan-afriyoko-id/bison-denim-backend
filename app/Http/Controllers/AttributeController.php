<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index(): JsonResponse
    {
        try {
            $attributes = Attribute::with('attributeValues')
                ->orderBy('sort', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Attributes retrieved successfully',
                'data' => $attributes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attributes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active attributes only.
     */
    public function getActive(): JsonResponse
    {
        try {
            $attributes = Attribute::with(['attributeValues' => function ($query) {
                $query->where('status', 'ACTIVE')->orderBy('sort', 'asc');
            }])
                ->where('status', 'ACTIVE')
                ->orderBy('sort', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Active attributes retrieved successfully',
                'data' => $attributes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving active attributes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:attributes,name',
                'sort' => 'nullable|integer|min:0',
                'status' => 'nullable|in:ACTIVE,INACTIVE',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['status'] = $validated['status'] ?? 'ACTIVE';
            $validated['sort'] = $validated['sort'] ?? 0;

            $attribute = Attribute::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully',
                'data' => $attribute,
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
                'message' => 'Error creating attribute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified attribute.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $attribute = Attribute::with('attributeValues')->find($id);

            if (!$attribute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attribute retrieved successfully',
                'data' => $attribute,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving attribute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $attribute = Attribute::find($id);

            if (!$attribute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:100', Rule::unique('attributes', 'name')->ignore($id)],
                'sort' => 'nullable|integer|min:0',
                'status' => 'nullable|in:ACTIVE,INACTIVE',
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            $attribute->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully',
                'data' => $attribute->fresh(['attributeValues']),
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
                'message' => 'Error updating attribute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $attribute = Attribute::find($id);

            if (!$attribute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute not found',
                ], 404);
            }

            $attribute->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attribute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

