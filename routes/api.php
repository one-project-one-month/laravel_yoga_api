<?php

use App\Http\Controllers\Dashboard\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\TestimonialController;

//Public route
Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::prefix('v1/')->group(function () {
    //Admin
    Route::middleware(['auth:sanctum', 'adminMiddleware'])->group(function () {
        //user route
        Route::resource('users', UserController::class)->only('store', 'show', 'update', 'index');

        //role route
        Route::get('/roles', [RoleController::class, 'index']);

        //payment route
        Route::resource('payments', PaymentController::class);

        //appointment route
        Route::apiResource('appointments', AppointmentController::class);

        //trainer route
        Route::apiResource('/trainers', TrainerController::class);

        //testimonials route
        Route::apiResource('/testimonials', TestimonialController::class);

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

