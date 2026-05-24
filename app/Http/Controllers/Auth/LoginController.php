<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Resources\AuthResource\AuthResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * LoginController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle a login request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['These credentials do not match our records.'],
                ],
            ], 401);
        }

        // Check if email is verified
        // if (!$user->hasVerifiedEmail()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Email not verified',
        //         'data' => [
        //             'email' => $user->email,
        //             'status' => 'email_not_verified',
        //             'message' => 'Please verify your email before logging in. Check your inbox for verification link.',
        //         ],
        //     ], 403);
        // }

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new AuthResource($user),
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Handle a logout request.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if ($user) {
            // Revoke current token
            $user->currentAccessToken()?->delete();

            // Or revoke all tokens
            // $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Get authenticated user.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new AuthResource($user),
            ],
        ], 200);
    }
}

