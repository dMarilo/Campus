<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Creates a new user and an associated profile (Admin Only).
     *
     * This endpoint:
     *  - Validates user credentials and profile information
     *  - Creates a new user record with a temporary password
     *  - Automatically creates a corresponding profile
     *    based on the selected user type (student or professor)
     *  - Generates a verification token
     *  - Sends a welcome email with verification link and temporary password
     *  - Ensures both user and profile creation occur atomically
     *    within a database transaction
     *
     * The created user must verify their email and set a new password
     * before they can log in.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            // Store the plain password for email (before hashing)
            $temporaryPassword = $validated['password'];

            // 1️⃣ Create user with temporary password
            $user = User::create([
                'email'    => $validated['email'],
                'password' => $temporaryPassword, // Will be auto-hashed
                'type'     => $validated['type'],
                'status'   => User::STATUS_INACTIVE, // Inactive until verified
            ]);

            // 2️⃣ Generate verification token
            $verificationToken = $user->generateVerificationToken();

            // 3️⃣ Create profile shell
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

            // 4️⃣ Send welcome email with verification link
            $user->notify(new WelcomeUserNotification($verificationToken, $temporaryPassword));

            return response()->json([
                'message' => 'User created successfully. Verification email sent.',
                'data' => [
                    'id'     => $user->id,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'status' => $user->status,
                ],
            ], Response::HTTP_CREATED);
        });
    }

    /**
     * Get the authenticated user's profile with full details.
     *
     * This endpoint:
     *  - Returns the current user's profile information
     *  - Includes student or professor profile data based on user type
     *  - Computes display name and avatar URL
     *  - Works for all user types (admin, student, professor)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth()->user();

        // Eager load the appropriate profile relationship
        if ($user->type === User::TYPE_STUDENT) {
            $user->load('student');
        } elseif ($user->type === User::TYPE_PROFESSOR) {
            $user->load('professor');
        }

        // Compute name and avatar
        $name = $this->computeUserName($user);
        $avatar = $this->computeAvatarUrl($name);

        // Build response
        $response = [
            'id' => $user->id,
            'email' => $user->email,
            'type' => $user->type,
            'status' => $user->status,
            'name' => $name,
            'avatar' => $avatar,
        ];

        // Add profile data if available
        if ($user->type === User::TYPE_STUDENT && $user->student) {
            $response['profile'] = $user->student;
        } elseif ($user->type === User::TYPE_PROFESSOR && $user->professor) {
            $response['profile'] = $user->professor;
        }

        return response()->json([
            'data' => $response,
        ]);
    }

    /**
     * Compute the display name for a user.
     *
     * @param User $user
     * @return string
     */
    private function computeUserName(User $user): string
    {
        if ($user->type === User::TYPE_STUDENT && $user->student) {
            return $user->student->fullName();
        }

        if ($user->type === User::TYPE_PROFESSOR && $user->professor) {
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
}
