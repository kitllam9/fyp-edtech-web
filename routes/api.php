<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\UserController;


Route::get('/test', function () {
    return response()->json(['message' => 'Hello world']);
});

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'getUser']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::prefix('content')->group(function () {
    Route::get('/', [ContentController::class, 'search']);
    Route::get('/get-pdf/{id}', [ContentController::class, 'getPdf']);
});
