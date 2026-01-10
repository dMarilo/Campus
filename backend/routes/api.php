<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\DormController;

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

    Route::post('/users', [UserController::class, 'store']);

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



});
