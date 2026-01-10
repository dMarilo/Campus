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
     *  - Attempts authentication using the JWT guard
     *  - Returns a JWT token if authentication is successful
     *  - Returns an unauthorized response if credentials are invalid
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

        return response()->json([
            'data' => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id'     => $user->id,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'status' => $user->status,
                ],
            ],
        ]);
    }
}
