<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CmsForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'CMS user not found'
            ], 404);
        }

        $randomPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($randomPassword),
        ]);

        Mail::raw(
            "Your new CMS password: {$randomPassword}",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('CMS Password Reset');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'New password has been sent to email'
        ]);
    }
}
