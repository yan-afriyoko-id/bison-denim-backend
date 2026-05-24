<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainBannerRequest\StoreMainBannerRequest;
use App\Http\Requests\MainBannerRequest\UpdateMainBannerRequest;
use App\Http\Resources\MainBannerResource\MainBannerResource;
use App\Interfaces\MainBannerRepositoryInterface;
use App\Models\MainBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicMainBannerController extends Controller
{
    protected MainBannerRepositoryInterface $mainBannerRepository;

    public function __construct(MainBannerRepositoryInterface $mainBannerRepository)
    {
        $this->mainBannerRepository = $mainBannerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 'all');

        if ($perPage === 'all') {
            $banners = MainBanner::active()->ordered()->get();
        } else {
            $banners = MainBanner::active()->ordered()->paginate((int) $perPage);
        }

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }
}
