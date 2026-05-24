<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingAddressResource;
use App\Interfaces\ShippingAddressRepositoryInterface;
use App\Models\UserShippingAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
    /**
     * @var ShippingAddressRepositoryInterface
     */
    protected ShippingAddressRepositoryInterface $shippingAddressRepository;

    /**
     * ShippingAddressController constructor.
     *
     * @param ShippingAddressRepositoryInterface $shippingAddressRepository
     */
    public function __construct(ShippingAddressRepositoryInterface $shippingAddressRepository)
    {
        $this->shippingAddressRepository = $shippingAddressRepository;
    }

    /**
     * Get all shipping addresses for authenticated user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $addresses = $this->shippingAddressRepository->getByUserId($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Shipping addresses retrieved successfully',
            'data' => ShippingAddressResource::collection($addresses),
        ]);
    }

    /**
     * Get a specific shipping address.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        
        $address = $this->shippingAddressRepository->findById($id, $user->id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping address not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Shipping address retrieved successfully',
            'data' => new ShippingAddressResource($address),
        ]);
    }

    /**
     * Create a new shipping address.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $hasPrimary = UserShippingAddress::where('user_id', $user->id)
            ->where('is_primary', true)
            ->exists();

        $validated = $request->validate([
            'first_name' => 'required|string|max:250',
            'last_name' => 'nullable|string|max:250',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $cleaned = preg_replace('/[\s\-()]/', '', trim($value));

                    if (!preg_match('/^\+?[0-9]{2,15}$/', $cleaned)) {
                        $fail('Phone number must contain only digits and may start with +');
                    }
                },
            ],
            'email' => 'nullable|email|max:250',
            'label_place' => 'nullable|string|max:250',
            'address' => 'required|string',
            'city' => 'required|string|max:250',
            'province' => 'required|string|max:250',
            'postal_code' => 'required|string|max:50',
            'note_address' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'province_id' => 'nullable|integer|min:1',
            'province_label' => 'nullable|string|max:250',
            'city_id' => 'nullable|integer|min:1',
            'city_label' => 'nullable|string|max:250',
            'district_id' => 'nullable|integer|min:1',
            'district_label' => 'nullable|string|max:250',
            'sub_district_id' => 'nullable|integer|min:1',
            'sub_district_label' => 'nullable|string|max:250',
        ]);

        if (!$hasPrimary) {
            $data['is_primary'] = true;
        } else {
            $data['is_primary'] = false;
        }

        $address = $this->shippingAddressRepository->create([
            'user_id' => $user->id,
            ...$validated,
            ...$data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping address created successfully',
            'data' => new ShippingAddressResource($address),
        ], 201);
    }

    /**
     * Update a shipping address.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:250',
            'last_name' => 'nullable|string|max:250',
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    
                    $cleaned = preg_replace('/[^0-9+]/', '', trim($value));
                    
                    if (!preg_match('/^\+?[0-9]{2,15}$/', $cleaned)) {
                        $fail('Phone number must contain only digits and may start with +');
                    }
                },
            ],
            'email' => 'nullable|email|max:250',
            'label_place' => 'nullable|string|max:250',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|max:250',
            'province' => 'sometimes|required|string|max:250',
            'postal_code' => 'sometimes|required|string|max:50',
            'note_address' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            // Location IDs and Labels from RajaOngkir (optional but recommended)
            'province_id' => 'nullable|integer|min:1',
            'province_label' => 'nullable|string|max:250',
            'city_id' => 'nullable|integer|min:1',
            'city_label' => 'nullable|string|max:250',
            'district_id' => 'nullable|integer|min:1',
            'district_label' => 'nullable|string|max:250',
            'sub_district_id' => 'nullable|integer|min:1',
            'sub_district_label' => 'nullable|string|max:250',
        ]);

        $address = $this->shippingAddressRepository->update($id, $validated, $user->id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping address not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Shipping address updated successfully',
            'data' => new ShippingAddressResource($address),
        ]);
    }

    /**
     * Delete a shipping address.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();

        $deleted = $this->shippingAddressRepository->delete($id, $user->id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping address not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Shipping address deleted successfully',
        ]);
    }
}
