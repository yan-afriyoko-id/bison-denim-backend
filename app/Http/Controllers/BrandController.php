<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest\StoreBrandRequest;
use App\Http\Requests\BrandRequest\UpdateBrandRequest;
use App\Http\Resources\BrandResource\BrandResource;
use App\Interfaces\BrandRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * @var BrandRepositoryInterface
     */
    protected BrandRepositoryInterface $brandRepository;

    /**
     * BrandController constructor.
     *
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Display a listing of brands (paginated).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page');
        $page = $request->get('page', 1);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($perPage) {
            $brands = $this->brandRepository->paginate((int) $perPage, $sortBy, $sortDirection);
            return response()->json([
                'success' => true,
                'data' => [
                    'brands' => BrandResource::collection($brands->items()),
                    'pagination' => [
                        'current_page' => $brands->currentPage(),
                        'per_page' => $brands->perPage(),
                        'total' => $brands->total(),
                        'last_page' => $brands->lastPage(),
                        'from' => $brands->firstItem(),
                        'to' => $brands->lastItem(),
                    ],
                    'sort' => [
                        'sort_by' => $sortBy,
                        'sort_direction' => $sortDirection,
                    ],
                ],
            ], 200);
        } else {
            $brands = $this->brandRepository->all($sortBy, $sortDirection);
            return response()->json([
                'success' => true,
                'data' => [
                    'brands' => BrandResource::collection($brands),
                    'total' => $brands->count(),
                    'sort' => [
                        'sort_by' => $sortBy,
                        'sort_direction' => $sortDirection,
                    ],
                ],
            ], 200);
        }
    }

    /**
     * Get all brands without pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $brands = $this->brandRepository->all();

        return response()->json([
            'success' => true,
            'data' => [
                'brands' => BrandResource::collection($brands),
            ],
        ], 200);
    }

    /**
     * Get active brands only (for public/homepage use).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActive(Request $request): JsonResponse
    {
        $brands = \App\Models\Brand::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'brands' => BrandResource::collection($brands),
            ],
        ], 200);
    }

    /**
     * Store a newly created brand.
     *
     * @param StoreBrandRequest $request
     * @return JsonResponse
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('brands', 'public');
            $data['logo'] = asset('storage/' . $path);
        } else {
            unset($data['logo']);
        }

        $brand = $this->brandRepository->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully',
            'data' => [
                'brand' => new BrandResource($brand),
            ],
        ], 201);
    }

    /**
     * Display the specified brand.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'brand' => new BrandResource($brand),
            ],
        ], 200);
    }

    /**
     * Update the specified brand.
     *
     * @param UpdateBrandRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        $allData = $request->all();
        $allowedFields = ['name', 'status', 'order', 'description'];
        $data = array_intersect_key($allData, array_flip($allowedFields));

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('brands', 'public');
            $data['logo'] = asset('storage/' . $path);
        }

        if (isset($data['order']) && $data['order'] !== null) {
            $data['order'] = (int) $data['order'];
        }

        $updatedBrand = $this->brandRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully',
            'data' => [
                'brand' => new BrandResource($updatedBrand),
            ],
        ], 200);
    }

    /**
     * Remove the specified brand.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        $this->brandRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully',
        ], 200);
    }
}
