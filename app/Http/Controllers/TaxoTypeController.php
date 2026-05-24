<?php

namespace App\Http\Controllers;

use App\Repositories\TaxoTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxoTypeController extends Controller
{
    protected $taxoTypeRepository;

    public function __construct(TaxoTypeRepository $taxoTypeRepository)
    {
        $this->taxoTypeRepository = $taxoTypeRepository;
    }

    /**
     * Display a listing of taxonomy types.
     */
    public function index(): JsonResponse
    {
        try {
            $taxoTypes = $this->taxoTypeRepository->all();

            return response()->json([
                'success' => true,
                'message' => 'Taxonomy types retrieved successfully',
                'data' => $taxoTypes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving taxonomy types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created taxonomy type in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'taxo_type_name' => 'required|string|max:100',
                'taxo_type_description' => 'nullable|string',
            ]);

            $taxoType = $this->taxoTypeRepository->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Taxonomy type created successfully',
                'data' => $taxoType,
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
                'message' => 'Error creating taxonomy type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified taxonomy type.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $taxoType = $this->taxoTypeRepository->findById($id);

            if (!$taxoType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taxonomy type not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Taxonomy type retrieved successfully',
                'data' => $taxoType,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving taxonomy type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified taxonomy type in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'taxo_type_name' => 'sometimes|required|string|max:100',
                'taxo_type_description' => 'nullable|string',
            ]);

            $taxoType = $this->taxoTypeRepository->update($id, $validated);

            if (!$taxoType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taxonomy type not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Taxonomy type updated successfully',
                'data' => $taxoType,
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
                'message' => 'Error updating taxonomy type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified taxonomy type from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->taxoTypeRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taxonomy type not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Taxonomy type deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting taxonomy type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
