<?php

use App\DataProcessing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\QuestController;
use App\Http\Controllers\Api\UserController;


Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [UserController::class, 'getUser']);
        Route::put('/', [UserController::class, 'update']);
        Route::get('/rank', [UserController::class, 'getRanking']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('content')->group(function () {
    Route::get('/get-pdf/{id}', [ContentController::class, 'getPdf']);
    Route::get('/grade', [ContentController::class, 'grade']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [ContentController::class, 'search']);
        Route::get('/complete/{id}', [ContentController::class, 'complete']);
    });
});

Route::prefix('badge')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [BadgeController::class, 'getBadges']);
        Route::get('/check', [BadgeController::class, 'checkUpdate']);
    });
});

Route::prefix('quest')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [QuestController::class, 'getQuestsWithStatus']);
        Route::get('/complete/{id}', [QuestController::class, 'complete']);
    });
});
