<?php

namespace App\Http\Controllers;

use App\Http\Requests\MainBannerRequest\StoreMainBannerRequest;
use App\Http\Requests\MainBannerRequest\UpdateMainBannerRequest;
use App\Http\Resources\MainBannerResource\MainBannerResource;
use App\Interfaces\MainBannerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainBannerController extends Controller
{
    protected MainBannerRepositoryInterface $mainBannerRepository;

    public function __construct(MainBannerRepositoryInterface $mainBannerRepository)
    {
        $this->mainBannerRepository = $mainBannerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $status = $request->get('status');

        $banners = $this->mainBannerRepository->paginate((int) $perPage, $sortBy, $sortDirection);

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => MainBannerResource::collection($banners->items()),
                'pagination' => [
                    'current_page' => $banners->currentPage(),
                    'per_page' => $banners->perPage(),
                    'total' => $banners->total(),
                    'last_page' => $banners->lastPage(),
                    'from' => $banners->firstItem(),
                    'to' => $banners->lastItem(),
                ],
            ],
        ], 200);
    }

    public function getActive(): JsonResponse
    {
        $banners = $this->mainBannerRepository->getActive();

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => MainBannerResource::collection($banners),
            ],
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $banner = $this->mainBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'banner' => new MainBannerResource($banner),
            ],
        ], 200);
    }

    public function store(StoreMainBannerRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('main-banners', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $banner = $this->mainBannerRepository->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Main banner created successfully',
            'data' => [
                'banner' => new MainBannerResource($banner),
            ],
        ], 201);
    }

    public function update(UpdateMainBannerRequest $request, int $id): JsonResponse
    {
        $banner = $this->mainBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                $oldPath = str_replace(asset('storage/'), '', $banner->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $path = $file->store('main-banners', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $updatedBanner = $this->mainBannerRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Main banner updated successfully',
            'data' => [
                'banner' => new MainBannerResource($updatedBanner),
            ],
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $banner = $this->mainBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // Delete image
        if ($banner->image) {
            $oldPath = str_replace(asset('storage/'), '', $banner->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $this->mainBannerRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Main banner deleted successfully',
        ], 200);
    }
}