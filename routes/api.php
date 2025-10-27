<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('update-password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Article Management Routes
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

    // User Preferences Settings
    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences'])->name('set-preferences');
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences'])->name('get-preferences');
});
