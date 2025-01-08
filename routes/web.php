<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::middleware('auth')->prefix('content')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->name('content');
        Route::get('/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/store', [ContentController::class, 'store'])->name('content.store');
        Route::get('/edit/{content}', [ContentController::class, 'edit'])->name('content.edit');
        Route::post('/update/{content}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/delete/{content}', [ContentController::class, 'destroy'])->name('content.delete');
        Route::post('/temp', [ContentController::class, 'temp'])->name('content.temp');
    });

    Route::prefix('content')->group(function () {});

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
