<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::prefix('content')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->name('content');
        Route::get('/create', [ContentController::class, 'create'])->name('content.create');
        // Route::get('/', [ContentController::class, 'index'])->name('content');
    });

    Route::prefix('quest')->group(function () {
        Route::get('/', [QuestController::class, 'index'])->name('quest');
        Route::get('/create', [QuestController::class, 'create'])->name('quest.create');
        // Route::get('/', [ContentController::class, 'index'])->name('content');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
