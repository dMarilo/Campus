<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // ⚠️ Auth & admin check will be added later
        // For now we assume the caller is an admin

        $validated = $request->validate([
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'type'     => ['required', 'string', 'in:' . implode(',', [
                User::TYPE_ADMIN,
                User::TYPE_PROFESSOR,
                User::TYPE_STUDENT,
            ])],
        ]);

        $user = User::create([
            'email'    => $validated['email'],
            'password' => $validated['password'], // auto-hashed
            'type'     => $validated['type'],
            'status'   => User::STATUS_ACTIVE,
        ]);

        return response()->json([
            'data' => [
                'id'     => $user->id,
                'email'  => $user->email,
                'type'   => $user->type,
                'status' => $user->status,
            ]
        ], Response::HTTP_CREATED);
    }
}
