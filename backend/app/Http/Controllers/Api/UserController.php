<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'type'       => ['required', 'string', 'in:' . implode(',', [
                User::TYPE_ADMIN,
                User::TYPE_PROFESSOR,
                User::TYPE_STUDENT,
            ])],
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {

            // 1️⃣ Create user (always)
            $user = User::create([
                'email'    => $validated['email'],
                'password' => $validated['password'], // auto-hashed
                'type'     => $validated['type'],
                'status'   => User::STATUS_ACTIVE,
            ]);

            // 2️⃣ Create profile shell
            if ($user->type === User::TYPE_STUDENT) {
                Student::create([
                    'user_id'    => $user->id,
                    'email'      => $user->email,
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'status'     => Student::STATUS_ACTIVE,
                ]);
            }

            if ($user->type === User::TYPE_PROFESSOR) {
                Professor::create([
                    'user_id'    => $user->id,
                    'email'      => $user->email,
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'status'     => Professor::STATUS_ACTIVE,
                ]);
            }

            return response()->json([
                'data' => [
                    'id'     => $user->id,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'status' => $user->status,
                ],
            ], Response::HTTP_CREATED);
        });
    }
}
