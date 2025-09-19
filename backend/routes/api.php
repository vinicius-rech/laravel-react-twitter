<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TweetController;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'getCurrentUser'])->name('user.current');
    Route::get('tweets', [TweetController::class, 'index'])->name('tweets.index');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('tweets', TweetController::class)->except(['index']);
});
