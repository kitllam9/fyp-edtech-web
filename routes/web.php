<?php

use App\DataProcessing;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::prefix('content')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->name('content');
        Route::get('/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/store', [ContentController::class, 'store'])->name('content.store');
        Route::get('/edit/{content}', [ContentController::class, 'edit'])->name('content.edit');
        Route::post('/update/{content}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/delete/{content}', [ContentController::class, 'destroy'])->name('content.delete');
        Route::post('/temp', [ContentController::class, 'temp'])->name('content.temp');
    });

    Route::prefix('badge')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('badges');
        Route::get('/create', [BadgeController::class, 'create'])->name('badge.create');
        Route::post('/store', [BadgeController::class, 'store'])->name('badge.store');
        Route::get('/edit/{badge}', [BadgeController::class, 'edit'])->name('badge.edit');
        Route::post('/update/{badge}', [BadgeController::class, 'update'])->name('badge.update');
        Route::delete('/delete/{badge}', [BadgeController::class, 'destroy'])->name('badge.delete');
    });

    Route::prefix('quest')->group(function () {
        Route::get('/', [QuestController::class, 'index'])->name('quests');
        Route::get('/create', [QuestController::class, 'create'])->name('quest.create');
        Route::post('/store', [QuestController::class, 'store'])->name('quest.store');
        Route::get('/edit/{quest}', [QuestController::class, 'edit'])->name('quest.edit');
        Route::post('/update/{quest}', [QuestController::class, 'update'])->name('quest.update');
        Route::delete('/delete/{quest}', [QuestController::class, 'destroy'])->name('quest.delete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::any('/model-training', [DataProcessing::class, 'train']);
// Route::any('/predict', [DataProcessing::class, 'predict']);

Route::any('/cluster', [DataProcessing::class, 'userClustering']);
Route::any('/test/tfidf', [DataProcessing::class, 'tfidfTest']);
Route::any('/test/lda', [DataProcessing::class, 'ldaTest']);

require __DIR__ . '/auth.php';
