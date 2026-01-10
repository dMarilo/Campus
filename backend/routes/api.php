<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LibraryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'campus-api',
    ]);
});

Route::post('/users', [UserController::class, 'store']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::prefix('books')->group(function () {
    Route::get('/', [LibraryController::class, 'index']);
    Route::get('/search', [LibraryController::class, 'search']);
    Route::get('/{id}', [LibraryController::class, 'show']);
    Route::post('/', [LibraryController::class, 'store']);
    Route::put('/{id}', [LibraryController::class, 'update']);
    Route::delete('/{id}', [LibraryController::class, 'destroy']);
});
