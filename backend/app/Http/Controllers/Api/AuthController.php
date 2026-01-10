<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
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
