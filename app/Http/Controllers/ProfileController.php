<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\UpdatePasswordRequest;
use App\Http\Requests\UserRequest\ResetPasswordRequest;
use App\Http\Resources\UserResource\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * ProfileController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        // Pindahkan ke sini jika parent Controller mendukung method middleware()
        // $this->middleware('auth:sanctum');
    }

    public function middleware()
    {
        return [
            'auth:sanctum'
        ];
    }

    /**
     * Get authenticated user profile.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Load relationships untuk roles
        $user->load(['roles']);

        // Get all permissions (direct + via roles) menggunakan Spatie
        // Ini penting karena user mungkin tidak punya direct permissions,
        // tapi punya permissions dari roles
        $allPermissions = $user->getAllPermissions();

        // Attach all permissions to user untuk response
        $user->setRelation('permissions', $allPermissions);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200);
    }

    /**
     * Update user password.
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $result = $this->userRepository->updatePassword($user->id, $request->new_password);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'user' => new UserResource($user->fresh()),
            ],
        ], 200);
    }

    /**
     * Reset user password (admin only).
     *
     * @param int $userId
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(int $userId, ResetPasswordRequest $request): JsonResponse
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $result = $this->userRepository->resetPassword($userId, $request->new_password);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'data' => [
                'user' => new UserResource($user->fresh()),
            ],
        ], 200);
    }

    /**
     * Update user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
        public function updateProfile(\Illuminate\Http\Request $request): JsonResponse
        {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:MALE,FEMALE,OTHER',
                'avatar' => 'nullable|string',
            ]);

            // Filter null and empty string values - hanya update field yang diisi
            // Ini mencegah error NOT NULL constraint untuk field yang required di database
            $data = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            // Jika tidak ada data yang di-update
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid fields to update',
                ], 422);
            }

            $updatedUser = $this->userRepository->updateProfile($user->id, $data);

            if (!$updatedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update profile',
                ], 400);
            }

            // Load relationships setelah update
            $updatedUser->load(['roles']);

            // Get all permissions (direct + via roles) menggunakan Spatie
            $allPermissions = $updatedUser->getAllPermissions();
            $updatedUser->setRelation('permissions', $allPermissions);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => new UserResource($updatedUser),
                ],
            ], 200);
        }
}

