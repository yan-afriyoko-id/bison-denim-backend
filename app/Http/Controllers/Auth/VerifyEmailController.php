<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyEmailController extends Controller
{
    /**
     * Verify email with token.
     *
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function verify(Request $request, string $token): JsonResponse
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email parameter is required.',
            ], 400);
        }

        // Find verification token
        $verification = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification token.',
            ], 400);
        }

        // Check token expiration (24 hours)
        if (strtotime($verification->created_at) < strtotime('-24 hours')) {
            // Delete expired token
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $token)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Verification token has expired. Please register again.',
            ], 400);
        }

        // Mark email as verified
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->markEmailAsVerified();

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->delete();

        // Generate API token for auto-login
        $apiToken = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You are now logged in.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'token' => $apiToken,
            ],
        ], 200);
    }
}
