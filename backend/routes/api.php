<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'campus-api',
    ]);
});

Route::post('/users', [UserController::class, 'store']);
