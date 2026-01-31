<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email address using the token.
     *
     * This endpoint validates the verification token and marks
     * the email as verified, but does NOT complete the password reset.
     * The user must still call the setPassword endpoint.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $hashedToken = hash('sha256', $validated['token']);

        $user = User::where('verification_token', $hashedToken)
            ->where('verification_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired verification token.',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($user->verifyEmail($validated['token'])) {
            return response()->json([
                'message' => 'Email verified successfully. Please set your new password.',
                'data' => [
                    'email' => $user->email,
                    'must_reset_password' => $user->must_reset_password,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Email verification failed.',
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Set a new password after email verification.
     *
     * This endpoint allows the user to set a new password after
     * verifying their email. The user must provide their email
     * and the new password (with confirmation).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPassword(Request $request)
    {
        $validated = $request->validate([
            'email'                 => ['required', 'email', 'exists:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {
            $user = User::where('email', $validated['email'])->first();

            // Check if email is verified
            if (!$user->isEmailVerified()) {
                return response()->json([
                    'message' => 'Email must be verified before setting a new password.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if password reset is required
            if (!$user->needsPasswordReset()) {
                return response()->json([
                    'message' => 'Password reset is not required for this account.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Complete password reset and activate account
            $user->completePasswordReset($validated['password']);
            $user->update(['status' => User::STATUS_ACTIVE]);

            return response()->json([
                'message' => 'Password set successfully. You can now log in with your new password.',
                'data' => [
                    'email'  => $user->email,
                    'status' => $user->status,
                ],
            ]);
        });
    }

    /**
     * Resend the verification email.
     *
     * This endpoint allows resending the verification email
     * if the original email was not received or the token expired.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->isEmailVerified()) {
            return response()->json([
                'message' => 'Email is already verified.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Generate new token
        $verificationToken = $user->generateVerificationToken();

        // Note: We don't send the temporary password again for security
        // The user should contact support if they lost it
        $user->notify(new \App\Notifications\WelcomeUserNotification($verificationToken, '********'));

        return response()->json([
            'message' => 'Verification email resent successfully.',
        ]);
    }
}
