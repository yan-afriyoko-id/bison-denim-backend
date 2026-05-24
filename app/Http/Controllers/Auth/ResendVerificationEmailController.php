<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ResendVerificationEmailController extends Controller
{
    /**
     * Resend verification email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email.',
            'email.exists' => 'Email not found in our system.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if email already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.',
                'data' => [
                    'status' => 'already_verified',
                    'message' => 'Your email is already verified. Please login.',
                ],
            ], 422);
        }

        // Delete old verification token if exists
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Generate new verification token
        $verificationToken = Str::random(64);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $verificationToken,
            'created_at' => now(),
        ]);

        // Send verification email
        $user->notify(new VerifyEmailNotification($verificationToken, $user->email));

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully.',
            'data' => [
                'email' => $user->email,
                'status' => 'verification_email_sent',
                'message' => 'Check your inbox for verification link. Link will expire in 24 hours.',
            ],
        ], 200);
    }
}

