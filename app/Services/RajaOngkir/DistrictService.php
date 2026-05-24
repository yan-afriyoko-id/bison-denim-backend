<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DistrictService
{
    public function getDistricts(int $cityId, ?int $districtId = null): array
    {
        try {
            $methodUrl = '/destination/district/' . $cityId;

            $headers = [
                'Key' => RajaOngkirHelper::getToken(),
            ];

            $response = Http::withHeaders($headers)
                ->get(RajaOngkirHelper::getBaseUrl() . $methodUrl);

            $responseBody = json_decode($response->getBody(), true);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                if (isset($responseBody['meta']) && isset($responseBody['data'])) {
                    $districts = $responseBody['data'];
                    
                    if ($districtId) {
                        $districts = array_filter($districts, function($district) use ($districtId) {
                            return isset($district['id']) && $district['id'] == $districtId;
                        });
                        $districts = array_values($districts);
                    }
                    
                    return [
                        'success' => $responseBody['meta']['status'] === 'success',
                        'data' => $districts,
                        'message' => $responseBody['meta']['message'] ?? 'Successfully retrieved districts',
                    ];
                }
                
                if (isset($responseBody['success']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['success'],
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved districts',
                    ];
                }
                
                if (isset($responseBody['data'])) {
                    return [
                        'success' => true,
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved districts',
                    ];
                }
            }

            return [
                'success' => false,
                'data' => [],
                'message' => $responseBody['meta']['message'] ?? ($responseBody['message'] ?? 'Failed to retrieve districts'),
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir District API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error connecting to RajaOngkir API: ' . $e->getMessage(),
            ];
        }
    }
}
