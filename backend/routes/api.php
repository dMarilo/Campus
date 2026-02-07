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
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\TeachingController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamResultController;
use App\Http\Controllers\Api\ProfessorController;

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

Route::prefix('classrooms')->group(function () {
    Route::post('/{classroom}/start-session',[ClassroomController::class, 'startSession']);
    Route::post('/{classroom}/end-session',[ClassroomController::class, 'endSession']);
    Route::post('/{classroom}/check-in',[ClassroomController::class, 'studentCheckIn']);
});

Route::prefix('auth')->group(function () {
    Route::post('/verify-email', [EmailVerificationController::class, 'verify']);
    Route::post('/set-password', [EmailVerificationController::class, 'setPassword']);
    Route::post('/resend', [EmailVerificationController::class, 'resend']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
| All routes inside this group require a valid API token.
*/

Route::middleware('auth:api')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [UserController::class, 'profile']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | Books / Library
    |--------------------------------------------------------------------------
    | Manage books and their association with courses.
    */

    Route::prefix('books')->group(function () {
        Route::get('/search', [LibraryController::class, 'search']);
        Route::get('/', [LibraryController::class, 'index']);
        Route::get('/{id}', [LibraryController::class, 'show']);
        Route::post('/', [LibraryController::class, 'store']);
        Route::put('/{id}', [LibraryController::class, 'update']);
        Route::delete('/{id}', [LibraryController::class, 'destroy']);
        Route::get('/course/{courseId}', [LibraryController::class, 'byCourse']);
    });

    /*
    |--------------------------------------------------------------------------
    | Borrowings
    |--------------------------------------------------------------------------
    | Handle borrowing and returning of library books by students.
    */

    Route::prefix('borrowings')->group(function () {
        Route::post('/borrow', [BorrowingController::class, 'borrow']);
        Route::post('/return', [BorrowingController::class, 'return']);
        Route::post('/student', [BorrowingController::class, 'studentBorrowings']);
        Route::get('/active', [BorrowingController::class, 'allBorrowed']);
    });

    /*
    |--------------------------------------------------------------------------
    | Dorms
    |--------------------------------------------------------------------------
    | Manage dormitories and high-level dorm information.
    */

    Route::prefix('dorms')->group(function () {
        Route::get('/', [DormController::class, 'getAllDorms']);
        Route::get('/search', [DormController::class, 'searchDorms']);
        Route::get('/capacity/{id}', [DormController::class, 'getDormCapacity']);
        Route::get('/rooms/{id}', [DormController::class, 'getDormRoomCount']);
        Route::get('/{id}', [DormController::class, 'getDormById']);
        Route::post('/load', [DormController::class, 'loadDorm']);
        Route::put('/update/{id}', [DormController::class, 'updateDorm']);
        Route::delete('/delete/{id}', [DormController::class, 'deleteDorm']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    | Manage individual dorm rooms.
    */

    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'getAllRooms']);
        Route::get('/dorm/{dormId}', [RoomController::class, 'getRoomsByDormId']);
        Route::get('/capacity/{id}', [RoomController::class, 'getRoomCapacity']);
        Route::get('/search', [RoomController::class, 'searchRooms']);
        Route::get('/{id}', [RoomController::class, 'getRoomById']);
        Route::post('/load', [RoomController::class, 'loadRoom']);
        Route::put('/update/{id}', [RoomController::class, 'updateRoom']);
        Route::delete('/delete/{id}', [RoomController::class, 'deleteRoom']);
    });

    /*
    |--------------------------------------------------------------------------
    | Campus Buildings
    |--------------------------------------------------------------------------
    | Manage campus buildings (academic, administrative, etc.).
    */

    Route::prefix('buildings')->group(function () {
        Route::get('/', [BuildingController::class, 'index']);
        Route::get('/{code}', [BuildingController::class, 'showByCode']);
        Route::post('/', [BuildingController::class, 'store']);
        Route::put('/{id}', [BuildingController::class, 'update']);
        Route::delete('/{id}', [BuildingController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    | Manage academic courses (definitions, not instances).
    */

    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/code/{code}', [CourseController::class, 'showByCode']);
        Route::get('/department/{department}', [CourseController::class, 'showByDepartment']);
        Route::get('/active', [CourseController::class, 'active']);
        Route::get('/book/{bookId}', [CourseController::class, 'byBook']);
        Route::get('/{id}', [CourseController::class, 'showById']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
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
        Route::get('/', [CourseClassController::class, 'index']);
        Route::get('/{id}', [CourseClassController::class, 'show'])
            ->where('id', '[0-9]+');
        Route::get('/{classId}/students', [AttendanceController::class, 'studentsByClass']);
        Route::get('/students/{studentId}/classes', [AttendanceController::class, 'classesByStudent']);
        Route::get('/{classId}/professors', [TeachingController::class, 'professorsByClass']);
        Route::get('/professors/{professorId}/classes', [TeachingController::class, 'classesByProfessor']);

        // Exam-related routes for classes
        Route::get('/{classId}/exams', [ExamController::class, 'examsByClass']);
        Route::get('/{classId}/exams/dates', [ExamController::class, 'examDatesByClass']);
        Route::get('/{classId}/exam-results', [ExamResultController::class, 'classResults']);
    });

    /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    | Manage student records and retrieve student information.
    */

    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/code/{code}', [StudentController::class, 'showByCode']);
        Route::get('/year/{year}', [StudentController::class, 'showByYear']);
        Route::get('/{id}', [StudentController::class, 'show']);

        // Exam-related routes for students
        Route::get('/{studentId}/exams', [ExamController::class, 'examsByStudent']);
        Route::get('/{studentId}/exams/upcoming', [ExamController::class, 'upcomingExamsByStudent']);
        Route::get('/{studentId}/exam-results', [ExamResultController::class, 'studentResults']);
        Route::get('/{studentId}/exam-results/passed', [ExamResultController::class, 'passedResults']);
    });

    /*
    |--------------------------------------------------------------------------
    | Professors
    |--------------------------------------------------------------------------
    | Manage professor records and information.
    */

    Route::prefix('professors')->group(function () {
        Route::get('/', [ProfessorController::class, 'index']);
        Route::get('/search', [ProfessorController::class, 'search']);
        Route::get('/active', [ProfessorController::class, 'active']);
        Route::get('/code/{code}', [ProfessorController::class, 'showByCode']);
        Route::get('/department/{department}', [ProfessorController::class, 'showByDepartment']);
        Route::get('/{id}', [ProfessorController::class, 'show']);
        Route::post('/', [ProfessorController::class, 'store']);
        Route::put('/{id}', [ProfessorController::class, 'update']);
        Route::delete('/{id}', [ProfessorController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Exams
    |--------------------------------------------------------------------------
    | Manage exams and exam registration.
    */

    Route::prefix('exams')->group(function () {
        // Get all exams
        Route::get('/', [ExamController::class, 'index']);

        // Get single exam
        Route::get('/{id}', [ExamController::class, 'show']);

        // Create exam
        Route::post('/', [ExamController::class, 'store']);

        // Update exam
        Route::put('/{id}', [ExamController::class, 'update']);

        // Delete exam
        Route::delete('/{id}', [ExamController::class, 'destroy']);

        // Register student for exam
        Route::post('/register', [ExamController::class, 'registerStudent']);

        // Get exam statistics
        Route::get('/{id}/statistics', [ExamController::class, 'statistics']);

        // Change exam status
        Route::patch('/{id}/status', [ExamController::class, 'changeStatus']);
    });

    /*
    |--------------------------------------------------------------------------
    | Exam Results
    |--------------------------------------------------------------------------
    | Manage exam results and grading.
    */

    Route::prefix('exam-results')->group(function () {
        // Get single exam result
        Route::get('/{id}', [ExamResultController::class, 'show']);

        // Grade/update exam result
        Route::put('/{id}/grade', [ExamController::class, 'gradeExam']);

        // Delete exam result
        Route::delete('/{id}', [ExamResultController::class, 'destroy']);
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
