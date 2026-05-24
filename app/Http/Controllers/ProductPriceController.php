<?php

namespace App\Http\Controllers;

use App\Repositories\ProductPriceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    protected $priceRepository;

    public function __construct(ProductPriceRepository $priceRepository)
    {
        $this->priceRepository = $priceRepository;
    }

    /**
     * Display a listing of product prices.
     */
    public function index(): JsonResponse
    {
        try {
            $prices = $this->priceRepository->all();

            return response()->json([
                'success' => true,
                'message' => 'Product prices retrieved successfully',
                'data' => $prices,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product prices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get prices by product ID.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            $prices = $this->priceRepository->getByProductId($productId);

            return response()->json([
                'success' => true,
                'message' => 'Product prices retrieved successfully',
                'data' => $prices,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product prices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product price in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'start_qty' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
            ]);

            $price = $this->priceRepository->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product price created successfully',
                'data' => $price,
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
                'message' => 'Error creating product price',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product price.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $price = $this->priceRepository->findById($id);

            if (!$price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product price not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product price retrieved successfully',
                'data' => $price,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product price',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product price in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_qty' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
            ]);

            $price = $this->priceRepository->update($id, $validated);

            if (!$price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product price not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product price updated successfully',
                'data' => $price,
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
                'message' => 'Error updating product price',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product price from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->priceRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product price not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product price deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product price',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
