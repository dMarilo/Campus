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
     * List all users (Admin Only).
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $users]);
    }

    /**
     * Get a single user by ID (Admin Only).
     */
    public function show(int $id)
    {
        $user = User::findOrFail($id);

        return response()->json(['data' => $user]);
    }

    /**
     * Update a user (Admin Only).
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email'  => ['sometimes', 'email', 'unique:users,email,' . $id],
            'type'   => ['sometimes', 'string', 'in:' . implode(',', [
                User::TYPE_ADMIN,
                User::TYPE_PROFESSOR,
                User::TYPE_STUDENT,
            ])],
            'status' => ['sometimes', 'string'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully.',
            'data'    => $user,
        ]);
    }

    /**
     * Delete a user (Admin Only).
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
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
     * Update the authenticated user's own profile fields.
     *
     * Students can update: first_name, last_name, phone, date_of_birth, gender.
     * Professors can update: first_name, last_name, phone, office_location, office_hours.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if ($user->type === User::TYPE_STUDENT) {
            $user->load('student');

            if (!$user->student) {
                return response()->json(['message' => 'Student profile not found.'], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'first_name'    => ['sometimes', 'string', 'max:255'],
                'last_name'     => ['sometimes', 'string', 'max:255'],
                'phone'         => ['sometimes', 'nullable', 'string', 'max:50'],
                'date_of_birth' => ['sometimes', 'nullable', 'date'],
                'gender'        => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
            ]);

            $user->student->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully.',
                'data'    => $user->student->fresh(),
            ]);
        }

        if ($user->type === User::TYPE_PROFESSOR) {
            $user->load('professor');

            if (!$user->professor) {
                return response()->json(['message' => 'Professor profile not found.'], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'first_name'      => ['sometimes', 'string', 'max:255'],
                'last_name'       => ['sometimes', 'string', 'max:255'],
                'phone'           => ['sometimes', 'nullable', 'string', 'max:50'],
                'office_location' => ['sometimes', 'nullable', 'string', 'max:255'],
                'office_hours'    => ['sometimes', 'nullable', 'string', 'max:255'],
            ]);

            $user->professor->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully.',
                'data'    => $user->professor->fresh(),
            ]);
        }

        return response()->json(['message' => 'Profile updates are not supported for this account type.'], Response::HTTP_UNPROCESSABLE_ENTITY);
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
