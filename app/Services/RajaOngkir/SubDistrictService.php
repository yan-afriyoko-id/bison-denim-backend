<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubDistrictService
{
    public function getSubDistricts(int $districtId, ?int $subDistrictId = null): array
    {
        try {
            $methodUrl = '/destination/sub-district/' . $districtId;

            $headers = [
                'Key' => RajaOngkirHelper::getToken(),
            ];

            $response = Http::withHeaders($headers)
                ->get(RajaOngkirHelper::getBaseUrl() . $methodUrl);

            $responseBody = json_decode($response->getBody(), true);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                if (isset($responseBody['meta']) && isset($responseBody['data'])) {
                    $subDistricts = $responseBody['data'];
                    
                    if ($subDistrictId) {
                        $subDistricts = array_filter($subDistricts, function($subDistrict) use ($subDistrictId) {
                            return isset($subDistrict['id']) && $subDistrict['id'] == $subDistrictId;
                        });
                        $subDistricts = array_values($subDistricts);
                    }
                    
                    return [
                        'success' => $responseBody['meta']['status'] === 'success',
                        'data' => $subDistricts,
                        'message' => $responseBody['meta']['message'] ?? 'Successfully retrieved sub-districts',
                    ];
                }
                
                if (isset($responseBody['success']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['success'],
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved sub-districts',
                    ];
                }
                
                if (isset($responseBody['data'])) {
                    return [
                        'success' => true,
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved sub-districts',
                    ];
                }
            }

            return [
                'success' => false,
                'data' => [],
                'message' => $responseBody['meta']['message'] ?? ($responseBody['message'] ?? 'Failed to retrieve sub-districts'),
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir SubDistrict API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error connecting to RajaOngkir API: ' . $e->getMessage(),
            ];
        }
    }
}
