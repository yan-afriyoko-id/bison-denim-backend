<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * RegisterController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userRepository->create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'password' => $request->password,
        ]);

        // Generate email verification token
        $verificationToken = Str::random(64);
        
        // Store token in database
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $verificationToken,
            'created_at' => now(),
        ]);

        // Assign default role (User role)
        $user->assignRole('User');

        // Send verification email notification
        $user->notify(new VerifyEmailNotification($verificationToken, $user->email));

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'dob' => $user->dob->format('Y-m-d'),
                    'email_verified_at' => $user->email_verified_at,
                    'roles' => $user->getRoleNames(),
                ],
                'verification' => [
                    'status' => 'pending',
                    'message' => 'Verification link has been sent to your email. Please check your inbox and verify your account.',
                    'expires_in' => '24 hours',
                ],
            ],
        ], 201);
    }
}

