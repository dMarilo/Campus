<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Admin-only middleware will protect this later

        $validated = $request->validate([
            'email'          => ['required', 'email', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8'],
            'type'           => ['required', 'string', 'in:' . implode(',', [
                User::TYPE_ADMIN,
                User::TYPE_PROFESSOR,
                User::TYPE_STUDENT,
            ])],

            // Student-only fields (optional unless type = student)
            'first_name'     => ['required_if:type,' . User::TYPE_STUDENT, 'string'],
            'last_name'      => ['required_if:type,' . User::TYPE_STUDENT, 'string'],
            'student_index'  => ['required_if:type,' . User::TYPE_STUDENT, 'string'],
            'year_of_study'  => ['nullable', 'integer', 'min:1'],
            'department'     => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {

            // 1️⃣ Create user
            $user = User::create([
                'email'    => $validated['email'],
                'password' => $validated['password'], // auto-hashed
                'type'     => $validated['type'],
                'status'   => User::STATUS_ACTIVE,
            ]);

            // 2️⃣ Create student profile if needed
            if ($user->type === User::TYPE_STUDENT) {
                Student::create([
                    'user_id'       => $user->id,
                    'email'         => $user->email,
                    'first_name'    => $validated['first_name'],
                    'last_name'     => $validated['last_name'],
                    'student_index' => $validated['student_index'],
                    'year_of_study' => $validated['year_of_study'] ?? 1,
                    'department'    => $validated['department'] ?? null,
                    'status'        => Student::STATUS_ACTIVE,
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
