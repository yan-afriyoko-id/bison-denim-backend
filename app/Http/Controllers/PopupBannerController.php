<?php

namespace App\Http\Controllers;

use App\Http\Requests\PopupBannerRequest\StorePopupBannerRequest;
use App\Http\Requests\PopupBannerRequest\UpdatePopupBannerRequest;
use App\Http\Resources\PopupBannerResource\PopupBannerResource;
use App\Interfaces\PopupBannerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PopupBannerController extends Controller
{
    protected PopupBannerRepositoryInterface $popupBannerRepository;

    public function __construct(PopupBannerRepositoryInterface $popupBannerRepository)
    {
        $this->popupBannerRepository = $popupBannerRepository;
    }

    /**
     * CMS - Paginated list
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($perPage) {
            $banners = $this->popupBannerRepository
                ->paginate((int) $perPage, $sortBy, $sortDirection);

            $data = [
                'popup_banners' => PopupBannerResource::collection($banners->items()),
                'pagination' => [
                    'current_page' => $banners->currentPage(),
                    'per_page' => $banners->perPage(),
                    'total' => $banners->total(),
                    'last_page' => $banners->lastPage(),
                    'from' => $banners->firstItem(),
                    'to' => $banners->lastItem(),
                ],
            ];
        } else {
            $banners = $this->popupBannerRepository
                ->all($sortBy, $sortDirection);

            $data = [
                'popup_banners' => PopupBannerResource::collection($banners),
                'pagination' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data + [
                'sort' => [
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                ],
            ],
        ]);
    }

    /**
     * CMS - Store
     */
    public function store(StorePopupBannerRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('popup_banners', 'public');
            $data['image'] = asset('storage/' . $path);
        } else {
            unset($data['image']);
        }
        $banner = $this->popupBannerRepository->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Popup banner created successfully',
            'data' => [
                'popup_banner' => new PopupBannerResource($banner),
            ],
        ], 201);
    }

    /**
     * CMS - Show
     */
    public function show(int $id): JsonResponse
    {
        $banner = $this->popupBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Popup banner not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'popup_banner' => new PopupBannerResource($banner),
            ],
        ], 200);
    }

    /**
     * CMS - Update
     */
    public function update(UpdatePopupBannerRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $banner = $this->popupBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Popup banner not found',
            ], 404);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('popup_banners', 'public');
            $data['image'] = asset('storage/' . $path);
        }
        $updatedBanner = $this->popupBannerRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Popup banner updated successfully',
            'data' => [
                'popup_banner' => new PopupBannerResource($updatedBanner),
            ],
        ], 200);
    }

    /**
     * CMS - Delete
     */
    public function destroy(int $id): JsonResponse
    {
        $banner = $this->popupBannerRepository->findById($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Popup banner not found',
            ], 404);
        }

        $this->popupBannerRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Popup banner deleted successfully',
        ], 200);
    }

    /**
     * FE - Get random active banner
     */
    public function getRandomActive(): JsonResponse
    {
        $banner = $this->popupBannerRepository->getRandomActive();

        return response()->json([
            'success' => true,
            'data' => [
                'popup_banner' => $banner
                    ? new PopupBannerResource($banner)
                    : null,
            ],
        ], 200);
    }
}
