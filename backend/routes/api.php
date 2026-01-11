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

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'service' => 'campus-api',
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    //Route::post('/users', [UserController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Books
    |--------------------------------------------------------------------------
    */

    Route::prefix('books')->group(function () {
        Route::get('/', [LibraryController::class, 'index']);
        Route::get('/{id}', [LibraryController::class, 'show']);
        Route::post('/', [LibraryController::class, 'store']);
        Route::put('/{id}', [LibraryController::class, 'update']);
        Route::delete('/{id}', [LibraryController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Borrowings
    |--------------------------------------------------------------------------
    */

    Route::prefix('borrowings')->group(function () {
        Route::post('/borrow', [BorrowingController::class, 'borrow']);
        Route::post('/return', [BorrowingController::class, 'return']);
        Route::get('/student', [BorrowingController::class, 'studentBorrowings']);
        Route::get('/active', [BorrowingController::class, 'allBorrowed']);
    });

    /*
    |--------------------------------------------------------------------------
    | Dorms
    |--------------------------------------------------------------------------
    */

    Route::prefix('dorms')->group(function () {
        Route::get('/',               [DormController::class, 'getAllDorms']);
        Route::get('/search',        [DormController::class, 'searchDorms']);
        Route::get('/capacity/{id}', [DormController::class, 'getDormCapacity']);
        Route::get('/rooms/{id}',    [DormController::class, 'getDormRoomCount']);
        Route::get('/{id}',          [DormController::class, 'getDormById']);

        Route::post('/load',         [DormController::class, 'loadDorm']);
        Route::put('/update/{id}',   [DormController::class, 'updateDorm']);
        Route::delete('/delete/{id}', [DormController::class, 'deleteDorm']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    */

    Route::prefix('rooms')->group(function () {
        Route::get('/',                    [RoomController::class, 'getAllRooms']);
        Route::get('/dorm/{dormId}',      [RoomController::class, 'getRoomsByDormId']);
        Route::get('/capacity/{id}',      [RoomController::class, 'getRoomCapacity']);
        //????
        Route::get('/search',             [RoomController::class, 'searchRooms']);
        //------

        Route::get('/{id}',               [RoomController::class, 'getRoomById']);

        Route::post('/load',               [RoomController::class, 'loadRoom']);
        Route::put('/update/{id}',         [RoomController::class, 'updateRoom']);
        Route::delete('/delete/{id}',      [RoomController::class, 'deleteRoom']);
    });


    /*
    |--------------------------------------------------------------------------
    | Campus Buildings
    |--------------------------------------------------------------------------
    */

    Route::prefix('buildings')->group(function () {
        Route::get('/',          [BuildingController::class, 'index']);
        Route::get('/{code}',    [BuildingController::class, 'showByCode']);

        Route::post('/',         [BuildingController::class, 'store']);
        Route::put('/{id}',      [BuildingController::class, 'update']);
        Route::delete('/{id}',   [BuildingController::class, 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    */

    Route::prefix('courses')->group(function () {
        Route::get('/',                   [CourseController::class, 'index']);
        Route::get('/code/{code}',       [CourseController::class, 'showByCode']);
        Route::get('/department/{department}', [CourseController::class, 'showByDepartment']);
        Route::get('/active',             [CourseController::class, 'active']);
        Route::get('/{id}',               [CourseController::class, 'showById']);

        Route::post('/',                   [CourseController::class, 'store']);
        Route::put('/{id}',               [CourseController::class, 'update']);
        Route::delete('/{id}',            [CourseController::class, 'destroy']);
    });

});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/admin/students/{id}', [StudentController::class, 'update']);
});
