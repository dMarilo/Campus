<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Authenticates a user and issues a JWT token.
     *
     * This endpoint:
     *  - Validates the provided email and password credentials
     *  - Checks if the user's email is verified
     *  - Checks if the user needs to reset their password
     *  - Attempts authentication using the JWT guard
     *  - Returns a JWT token if authentication is successful
     *  - Returns appropriate error responses for various failure scenarios
     *
     * The returned token must be used in the Authorization header
     * for all subsequent authenticated API requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login using JWT directly
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = JWTAuth::user();

        // Eager load profile relationships
        $user->load(['student', 'professor']);

        // Check if email is verified
        if (!$user->isEmailVerified()) {
            // Invalidate the token since we won't allow login
            try {
                JWTAuth::invalidate($token); // Wrapped in try-catch
            } catch (\Exception $e) {
                // If invalidation fails, just continue
            }

            return response()->json([
                'message' => 'Email not verified. Please check your email and verify your account.',
                'error_code' => 'EMAIL_NOT_VERIFIED',
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if password reset is required
        if ($user->needsPasswordReset()) {
            // Invalidate the token since we won't allow login
            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'You must set a new password before logging in. Please check your email for instructions.',
                'error_code' => 'PASSWORD_RESET_REQUIRED',
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if user account is active
        if (!$user->isActive()) {
            // Invalidate the token since we won't allow login
            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Your account is not active. Please contact support.',
                'error_code' => 'ACCOUNT_INACTIVE',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'data' => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id'     => $user->id,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'status' => $user->status,
                    'name'   => $this->computeUserName($user),
                    'avatar' => $this->computeAvatarUrl($this->computeUserName($user)),
                ],
            ],
        ]);
    }

    /**
     * Compute the display name for a user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    private function computeUserName($user): string
    {
        if ($user->type === \App\Models\User::TYPE_STUDENT && $user->student) {
            return $user->student->fullName();
        }

        if ($user->type === \App\Models\User::TYPE_PROFESSOR && $user->professor) {
            return $user->professor->fullName();
        }

        // Fallback for admin or users without profiles
        return explode('@', $user->email)[0];
    }

    /**
     * Generate avatar URL using ui-avatars.com API.
     *
     * @param string $name
     * @return string
     */
    private function computeAvatarUrl(string $name): string
    {
        $encodedName = urlencode($name);
        return "https://ui-avatars.com/api/?name={$encodedName}&size=200&background=667eea&color=fff&bold=true";
    }

    /**
     * Log out the user (invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh the JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'data' => [
                'token'      => $newToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }
}
