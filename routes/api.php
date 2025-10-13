<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;

Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::prefix('v1/')->group(function () {
    //Admin
    Route::middleware(['auth:sanctum', 'adminMiddleware'])->group(function () {
        //user route
        Route::resource('users', UserController::class)->only('store', 'show', 'update', 'index');
        //role route
        Route::get('/roles', [RoleController::class, 'index']);

    });
    //Trainer
    Route::middleware(['auth:sanctum', 'trainerMiddleware'])->group(function () {
        //user route
        Route::resource('users', UserController::class)->only('show', 'update');
    });

    //Student
    Route::middleware(['auth:sanctum', 'studentMiddleware'])->group(function () {
        //user route

    });
});
