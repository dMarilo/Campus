<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\DormController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseClassController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TeachingController;
use App\Http\Controllers\Api\ExamController;

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
| Simple endpoint used to verify that the API service is running.
| Useful for monitoring, CI pipelines, and load balancers.
*/

Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'service' => 'campus-api',
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication & Public User Actions
|--------------------------------------------------------------------------
| Routes that do not require authentication.
| Used for login and initial user creation.
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
| All routes inside this group require a valid API token.
*/

Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Books / Library
    |--------------------------------------------------------------------------
    | Manage books and their association with courses.
    */

    Route::prefix('books')->group(function () {

        // Get all books
        Route::get('/', [LibraryController::class, 'index']);

        // Get a single book by ID
        Route::get('/{id}', [LibraryController::class, 'show']);

        // Create a new book
        Route::post('/', [LibraryController::class, 'store']);

        // Update an existing book
        Route::put('/{id}', [LibraryController::class, 'update']);

        // Delete a book
        Route::delete('/{id}', [LibraryController::class, 'destroy']);

        // Get all books required for a specific course
        Route::get('/course/{courseId}', [LibraryController::class, 'byCourse']);
    });

    /*
    |--------------------------------------------------------------------------
    | Borrowings
    |--------------------------------------------------------------------------
    | Handle borrowing and returning of library books by students.
    */

    Route::prefix('borrowings')->group(function () {

        // Borrow a book
        Route::post('/borrow', [BorrowingController::class, 'borrow']);

        // Return a borrowed book
        Route::post('/return', [BorrowingController::class, 'return']);

        // Get borrowing history of the authenticated student
        Route::get('/student', [BorrowingController::class, 'studentBorrowings']);

        // Get all currently active borrowings
        Route::get('/active', [BorrowingController::class, 'allBorrowed']);
    });

    /*
    |--------------------------------------------------------------------------
    | Dorms
    |--------------------------------------------------------------------------
    | Manage dormitories and high-level dorm information.
    */

    Route::prefix('dorms')->group(function () {

        // Get all dorms
        Route::get('/', [DormController::class, 'getAllDorms']);

        // Search dorms by criteria
        Route::get('/search', [DormController::class, 'searchDorms']);

        // Get total capacity of a dorm
        Route::get('/capacity/{id}', [DormController::class, 'getDormCapacity']);

        // Get number of rooms in a dorm
        Route::get('/rooms/{id}', [DormController::class, 'getDormRoomCount']);

        // Get dorm by ID
        Route::get('/{id}', [DormController::class, 'getDormById']);

        // Create a new dorm
        Route::post('/load', [DormController::class, 'loadDorm']);

        // Update an existing dorm
        Route::put('/update/{id}', [DormController::class, 'updateDorm']);

        // Delete a dorm
        Route::delete('/delete/{id}', [DormController::class, 'deleteDorm']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    | Manage individual dorm rooms.
    */

    Route::prefix('rooms')->group(function () {

        // Get all rooms
        Route::get('/', [RoomController::class, 'getAllRooms']);

        // Get all rooms in a specific dorm
        Route::get('/dorm/{dormId}', [RoomController::class, 'getRoomsByDormId']);

        // Get room capacity
        Route::get('/capacity/{id}', [RoomController::class, 'getRoomCapacity']);

        // Search rooms by criteria
        Route::get('/search', [RoomController::class, 'searchRooms']);

        // Get room by ID
        Route::get('/{id}', [RoomController::class, 'getRoomById']);

        // Create a new room
        Route::post('/load', [RoomController::class, 'loadRoom']);

        // Update a room
        Route::put('/update/{id}', [RoomController::class, 'updateRoom']);

        // Delete a room
        Route::delete('/delete/{id}', [RoomController::class, 'deleteRoom']);
    });

    /*
    |--------------------------------------------------------------------------
    | Campus Buildings
    |--------------------------------------------------------------------------
    | Manage campus buildings (academic, administrative, etc.).
    */

    Route::prefix('buildings')->group(function () {

        // Get all buildings
        Route::get('/', [BuildingController::class, 'index']);

        // Get building by code
        Route::get('/{code}', [BuildingController::class, 'showByCode']);

        // Create a building
        Route::post('/', [BuildingController::class, 'store']);

        // Update a building
        Route::put('/{id}', [BuildingController::class, 'update']);

        // Delete a building
        Route::delete('/{id}', [BuildingController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    | Manage academic courses (definitions, not instances).
    */

    Route::prefix('courses')->group(function () {

        // Get all courses
        Route::get('/', [CourseController::class, 'index']);

        // Get course by code
        Route::get('/code/{code}', [CourseController::class, 'showByCode']);

        // Get courses by department
        Route::get('/department/{department}', [CourseController::class, 'showByDepartment']);

        // Get only active courses
        Route::get('/active', [CourseController::class, 'active']);

        // Get course by ID
        Route::get('/{id}', [CourseController::class, 'showById']);

        // Create a course
        Route::post('/', [CourseController::class, 'store']);

        // Update a course
        Route::put('/{id}', [CourseController::class, 'update']);

        // Delete a course
        Route::delete('/{id}', [CourseController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Classes (Course Instances)
    |--------------------------------------------------------------------------
    | Manage concrete offerings of courses in a semester.
    | Includes attendance (students) and teaching (professors).
    */

    Route::prefix('classes')->group(function () {

        // Get all classes
        Route::get('/', [CourseClassController::class, 'index']);

        // Get all students attending a specific class
        Route::get('/{classId}/students', [AttendanceController::class, 'studentsByClass']);

        // Get all classes attended by a specific student
        Route::get('/students/{studentId}/classes', [AttendanceController::class, 'classesByStudent']);

        // Get all professors teaching a specific class
        Route::get('/{classId}/professors', [TeachingController::class, 'professorsByClass']);

        // Get all classes taught by a specific professor
        Route::get('/professors/{professorId}/classes', [TeachingController::class, 'classesByProfessor']);

        Route::get('/{classId}/exams/dates', [ExamController::class, 'examDatesByClass']);
    });

});

/*
|--------------------------------------------------------------------------
| Admin-only Routes
|--------------------------------------------------------------------------
| Routes restricted to admin users only.
*/

Route::middleware(['auth:api', 'role:admin'])->group(function () {

    // Update student information (admin privileges required)
    Route::put('/admin/students/{id}', [StudentController::class, 'update']);
});
