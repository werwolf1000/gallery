<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/slider', [SliderController::class, 'index']);
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/orders', [AdminController::class, 'orders']);
        Route::patch('/orders/{order}', [AdminController::class, 'updateOrder']);
        Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder']);

        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::patch('/users/{user}', [AdminController::class, 'updateUser']);

        Route::get('/properties', [AdminController::class, 'properties']);
        Route::post('/properties', [AdminController::class, 'storeProperty']);
        Route::patch('/properties/{property}', [AdminController::class, 'updateProperty']);
        Route::delete('/properties/{property}', [AdminController::class, 'destroyProperty']);

        Route::get('/slides', [AdminController::class, 'slides']);
        Route::post('/slides', [AdminController::class, 'storeSlide']);
        Route::patch('/slides/{slide}', [AdminController::class, 'updateSlide']);
        Route::delete('/slides/{slide}', [AdminController::class, 'destroySlide']);
    });
});
