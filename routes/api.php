<?php

use App\Http\Controllers\Api\ForgetPasswordController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Client\PaymentHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\TrainerController;
use App\Http\Controllers\Dashboard\AppointmentController;
use App\Http\Controllers\Dashboard\TestimonialController;

//Public route
Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);
Route::post('/v1/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('v1/forget-password', [ForgetPasswordController::class, 'sendOtp']);
Route::post('v1/resend-otp', [ForgetPasswordController::class, 'resendOtp']);
Route::post('v1/verify-otp', [ForgetPasswordController::class, 'verifyOtp']);
Route::post('v1/reset-password', [ForgetPasswordController::class, 'resetPassword']);
Route::post('v1/verify-email-otp', [VerifyEmailController::class, 'sendEmailVerifyOtp']);
Route::post('v1/verify-email', [VerifyEmailController::class, 'verifyEmail']);

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

        //subscription
        Route::resource('subscriptions', SubscriptionController::class);

        //subscription user
        // Route::resource('/subscriptionUsers', SubscriptionUserController::class)->only(['index', 'update']);
    });

    //Trainer
    Route::middleware(['auth:sanctum', 'trainerMiddleware'])->group(function () {
        //user route
        Route::resource('users', UserController::class)->only('show', 'update');

    });

    //Student
    Route::middleware(['auth:sanctum', 'studentMiddleware'])->group(function () {
        //subscription route
        Route::post('/users/{id}/subscriptions', [PaymentHistoryController::class, 'store']);
        Route::get('/users/{id}/subscriptions', [PaymentHistoryController::class, 'show']);
    });
});

