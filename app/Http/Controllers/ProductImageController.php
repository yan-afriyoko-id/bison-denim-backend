<?php

namespace App\Http\Controllers;

use App\Repositories\ProductImageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    protected $imageRepository;

    public function __construct(ProductImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * Display a listing of product images.
     */
    public function index(): JsonResponse
    {
        try {
            $images = $this->imageRepository->all();

            // Transform images to include full URL
            $transformedImages = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'fk_product_id' => $image->fk_product_id,
                    'path' => $image->path ? asset($image->path) : null,
                    'order_number' => $image->order_number,
                    'is_featured' => $image->is_featured,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product images retrieved successfully',
                'data' => $transformedImages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product images',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get images by product ID.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            $images = $this->imageRepository->getByProductId($productId);

            // Transform images to include full URL
            $transformedImages = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'fk_product_id' => $image->fk_product_id,
                    'path' => $image->path ? asset($image->path) : null,
                    'order_number' => $image->order_number,
                    'is_featured' => $image->is_featured,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product images retrieved successfully',
                'data' => [
                    'images' => $transformedImages,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product images',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product image in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fk_product_id' => 'required|integer|exists:products,id',
                'path' => 'required|string',
                'order_number' => 'nullable|integer|min:0',
            ]);

            $image = $this->imageRepository->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product image created successfully',
                'data' => $image,
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
                'message' => 'Error creating product image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product image.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $image = $this->imageRepository->findById($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image not found',
                ], 404);
            }

            // Transform image to include full URL
            $transformedImage = [
                'id' => $image->id,
                'fk_product_id' => $image->fk_product_id,
                'path' => $image->path ? asset($image->path) : null,
                'order_number' => $image->order_number,
                'is_featured' => $image->is_featured,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Product image retrieved successfully',
                'data' => $transformedImage,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product image in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'path' => 'sometimes|required|string',
                'order_number' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
            ]);

            $image = $this->imageRepository->update($id, $validated);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image not found',
                ], 404);
            }

            // Transform image to include full URL
            $transformedImage = [
                'id' => $image->id,
                'fk_product_id' => $image->fk_product_id,
                'path' => $image->path ? asset($image->path) : null,
                'order_number' => $image->order_number,
                'is_featured' => $image->is_featured,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Product image updated successfully',
                'data' => $transformedImage,
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
                'message' => 'Error updating product image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product image from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->imageRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product image deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set an image as featured for a product.
     * This will automatically unset other featured images for the same product.
     */
    public function setFeatured(int $id): JsonResponse
    {
        try {
            $image = $this->imageRepository->findById($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image not found',
                ], 404);
            }

            // Set this image as featured (model boot will handle unsetting others)
            $image->is_featured = true;
            $image->save();

            // Refresh to get updated data
            $image = $image->fresh();

            // Transform image to include full URL
            $transformedImage = [
                'id' => $image->id,
                'fk_product_id' => $image->fk_product_id,
                'path' => $image->path ? asset($image->path) : null,
                'order_number' => $image->order_number,
                'is_featured' => $image->is_featured,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Product image set as featured successfully',
                'data' => $transformedImage,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting featured image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
