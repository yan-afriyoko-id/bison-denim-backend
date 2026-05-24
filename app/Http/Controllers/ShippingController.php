<?php

namespace App\Http\Controllers;

use App\Http\Requests\RajaOngkirRequest\GetCityRequest;
use App\Http\Requests\RajaOngkirRequest\GetCostRequest;
use App\Http\Requests\RajaOngkirRequest\GetDistrictRequest;
use App\Http\Requests\RajaOngkirRequest\GetProvinceRequest;
use App\Http\Requests\RajaOngkirRequest\GetSubDistrictRequest;
use App\Services\RajaOngkir\CityService;
use App\Services\RajaOngkir\CostService;
use App\Services\RajaOngkir\DistrictService;
use App\Services\RajaOngkir\ProvinceService;
use App\Services\RajaOngkir\SubDistrictService;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    protected ProvinceService $provinceService;
    protected CityService $cityService;
    protected DistrictService $districtService;
    protected SubDistrictService $subDistrictService;
    protected CostService $costService;

    public function __construct(
        ProvinceService $provinceService,
        CityService $cityService,
        DistrictService $districtService,
        SubDistrictService $subDistrictService,
        CostService $costService
    ) {
        $this->provinceService = $provinceService;
        $this->cityService = $cityService;
        $this->districtService = $districtService;
        $this->subDistrictService = $subDistrictService;
        $this->costService = $costService;
    }

    /**
     * Get provinces
     *
     * @param GetProvinceRequest $request
     * @return JsonResponse
     */
    public function getProvinces(GetProvinceRequest $request): JsonResponse
    {
        $provinceId = $request->input('id');
        $result = $this->provinceService->getProvinces($provinceId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => [],
        ], 400);
    }

    /**
     * Get cities
     *
     * @param GetCityRequest $request
     * @return JsonResponse
     */
    public function getCities(GetCityRequest $request): JsonResponse
    {
        $cityId = $request->input('id');
        $provinceId = $request->input('province_id');
        $result = $this->cityService->getCities($cityId, $provinceId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => [],
        ], 400);
    }

    /**
     * Get districts
     *
     * @param GetDistrictRequest $request
     * @return JsonResponse
     */
    public function getDistricts(GetDistrictRequest $request): JsonResponse
    {
        $cityId = $request->input('city_id');
        $districtId = $request->input('id');
        
        $result = $this->districtService->getDistricts($cityId, $districtId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => [],
        ], 400);
    }

    /**
     * Get sub-districts
     *
     * @param GetSubDistrictRequest $request
     * @return JsonResponse
     */
    public function getSubDistricts(GetSubDistrictRequest $request): JsonResponse
    {
        $districtId = $request->input('district_id');
        $subDistrictId = $request->input('id');
        
        $result = $this->subDistrictService->getSubDistricts($districtId, $subDistrictId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => [],
        ], 400);
    }

    public function getCost(GetCostRequest $request): JsonResponse
    {
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $courier = $request->input('courier');
        $origin = $request->input('origin');
        $price = $request->input('price');

        $result = $this->costService->getCost($destination, $weight, $courier, $origin, $price);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => [],
        ], 400);
    }
}
