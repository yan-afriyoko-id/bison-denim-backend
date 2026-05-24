<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\ConfigRepositoryInterface;
use App\Http\Resources\ConfigResource\ConfigResource;
use App\Http\Requests\ConfigRequest\StoreConfigRequest;
use App\Http\Requests\ConfigRequest\UpdateConfigRequest;

class ConfigController extends Controller
{
    public function __construct(
        protected ConfigRepositoryInterface $configRepository
    ) {}

    /**
     * Display a listing of all configs
     */
    public function index(): JsonResponse
    {
        try {
            $configs = $this->configRepository->getAll();
            return response()->json([
                'success' => true,
                'message' => 'Configurations retrieved successfully',
                'data' => ConfigResource::collection($configs),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve configurations',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get config by key
     */
    public function show(string $key): JsonResponse
    {
        try {
            $config = $this->configRepository->getByKey($key);
            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => "Configuration with key '{$key}' not found",
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'success' => true,
                'message' => 'Configuration retrieved successfully',
                'data' => new ConfigResource($config),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve configuration',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get public config by key (no authentication required)
     * Only allows access to public configs like store_logo_website, store_name, etc.
     */
    public function showPublic(string $key): JsonResponse
    {
        try {
            $publicConfigs = [
                'store_logo_website',
                'store_favicon',
                'store_name',
                'store_email',
                'store_phone',
                'store_address',
                'store_city',
                'store_province',
                'store_country',
                'store_postal_code',
                'store_currency',
                'app_name',
                'app_url',
                'social_instagram',
                'social_tiktok',
                'social_facebook',
                'social_youtube',
                'social_pinterest',
                'social_whatsapp',
                'topbanner',
                'product_protection'
            ];

            if (!in_array($key, $publicConfigs)) {
                return response()->json([
                    'success' => false,
                    'message' => "Configuration with key '{$key}' is not publicly accessible",
                ], Response::HTTP_FORBIDDEN);
            }

            $config = $this->configRepository->getByKey($key);
            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => "Configuration with key '{$key}' not found",
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuration retrieved successfully',
                'data' => new ConfigResource($config),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve configuration',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created config
     */
    public function store(StoreConfigRequest $request): JsonResponse
    {
        try {
            $config = $this->configRepository->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Configuration created successfully',
                'data' => new ConfigResource($config),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create configuration',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update config by key
     */
    public function update(string $key, Request $request): JsonResponse
    {
        try {
            // Determine if this is an image config
            $imageConfigs = ['store_logo_website', 'store_favicon'];
            $isImageConfig = in_array($key, $imageConfigs);

            // For file uploads, POST method is required (PUT doesn't support multipart/form-data properly)
            if ($isImageConfig && $request->method() === 'PUT' && $request->hasFile('value')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please use POST method for file uploads. PUT method does not support file uploads properly.',
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            // Validate request based on config type
            if ($isImageConfig) {
                $validator = Validator::make($request->all(), [
                    'value' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,ico|max:2048',
                    'description' => 'nullable|string|max:500',
                    'type' => 'nullable|in:string,integer,boolean,json,text',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                // Use FormRequest for non-image configs
                $updateRequest = new UpdateConfigRequest();
                $updateRequest->setContainer(app());
                $updateRequest->setRedirector(app('redirect'));
                $updateRequest->initialize($request->all(), $request->all(), [], [], [], $request->server->all());
                $updateRequest->setUserResolver($request->getUserResolver());
                $updateRequest->setRouteResolver($request->getRouteResolver());

                if (!$updateRequest->authorize()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                    ], Response::HTTP_FORBIDDEN);
                }

                $validator = Validator::make($request->all(), $updateRequest->rules(), $updateRequest->messages());

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            // Prepare payload
            $payload = [];

            // Debug: Check what we received
            $allFiles = $request->allFiles();

            // Handle file upload for image configs
            if ($isImageConfig) {
                // Check for file with key 'value' or 'file' (Postman sometimes uses 'file')
                $uploadedFile = null;
                if ($request->hasFile('value')) {
                    $uploadedFile = $request->file('value');
                } elseif ($request->hasFile('file')) {
                    $uploadedFile = $request->file('file');
                } elseif (!empty($allFiles)) {
                    // Get first file if any file is uploaded
                    $uploadedFile = reset($allFiles);
                }

                if ($uploadedFile) {
                    $payload['value'] = $uploadedFile;
                } else {
                    // If it's an image config but no file, return error
                    return response()->json([
                        'success' => false,
                        'message' => 'No file provided for image configuration. Please upload a file with field name "value" or "file".',
                        'debug' => [
                            'key' => $key,
                            'is_image_config' => $isImageConfig,
                            'has_file_value' => $request->hasFile('value'),
                            'has_file_file' => $request->hasFile('file'),
                            'all_files' => array_keys($allFiles),
                            'content_type' => $request->header('Content-Type'),
                            'method' => $request->method(),
                        ],
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                // For non-image configs, get value from input
                if ($request->filled('value')) {
                    $payload['value'] = $request->input('value');
                }
            }

            // Include other fields if provided
            if ($request->filled('description')) {
                $payload['description'] = $request->input('description');
            }
            if ($request->filled('type')) {
                $payload['type'] = $request->input('type');
            }

            // If no data provided at all, return error
            if (empty($payload)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided to update the configuration',
                    'debug' => [
                        'key' => $key,
                        'is_image_config' => $isImageConfig,
                        'all_files' => array_keys($allFiles),
                        'all_input_keys' => array_keys($request->all()),
                        'content_type' => $request->header('Content-Type'),
                        'method' => $request->method(),
                    ],
                ], Response::HTTP_BAD_REQUEST);
            }

            $config = $this->configRepository->updateByKey($key, $payload);
            return response()->json([
                'success' => true,
                'message' => 'Configuration updated successfully',
                'data' => new ConfigResource($config),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update configuration',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete config by key
     */
    public function destroy(string $key): JsonResponse
    {
        try {
            $deleted = $this->configRepository->deleteByKey($key);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => "Configuration with key '{$key}' not found",
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'success' => true,
                'message' => 'Configuration deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete configuration',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
