<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CityService
{
    public function getCities(?int $cityId = null, ?int $provinceId = null): array
    {
        try {
            if (!$provinceId) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Province ID is required',
                ];
            }

            $methodUrl = '/destination/city/' . $provinceId;

            $headers = [
                'Key' => RajaOngkirHelper::getToken(),
            ];

            $response = Http::withHeaders($headers)
                ->get(RajaOngkirHelper::getBaseUrl() . $methodUrl);

            $responseBody = json_decode($response->getBody(), true);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                if (isset($responseBody['meta']) && isset($responseBody['data'])) {
                    $cities = $responseBody['data'];
                    
                    if ($cityId) {
                        $cities = array_filter($cities, function($city) use ($cityId) {
                            return isset($city['id']) && $city['id'] == $cityId;
                        });
                        $cities = array_values($cities);
                    }
                    
                    return [
                        'success' => $responseBody['meta']['status'] === 'success',
                        'data' => $cities,
                        'message' => $responseBody['meta']['message'] ?? 'Successfully retrieved cities',
                    ];
                }
                
                if (isset($responseBody['success']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['success'],
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved cities',
                    ];
                }
                
                if (isset($responseBody['data'])) {
                    return [
                        'success' => true,
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved cities',
                    ];
                }
            }

            return [
                'success' => false,
                'data' => [],
                'message' => $responseBody['meta']['message'] ?? ($responseBody['message'] ?? 'Failed to retrieve cities'),
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir City API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error connecting to RajaOngkir API: ' . $e->getMessage(),
            ];
        }
    }
}
