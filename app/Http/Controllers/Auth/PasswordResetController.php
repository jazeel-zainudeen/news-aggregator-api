<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     tags={"Authentication"},
     *     summary="Send password reset link",
     *     description="Sends a password reset link to the user's email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Password reset link sent"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Failed to send reset link"))
     *     )
     * )
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent'])
            : response()->json(['message' => 'Failed to send reset link'], 422);
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset password",
     *     description="Resets the user's password using a valid token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="reset-token-here"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Password reset successfully"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid token",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Invalid token or email"))
     *     )
     * )
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully'])
            : response()->json(['message' => 'Invalid token or email'], 422);
    }
}
