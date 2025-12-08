<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgetPasswordController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\FoodController as ClientFoodController;
use App\Http\Controllers\Client\TestimonialController as ClientTestimonialController;
use App\Http\Controllers\Client\UserSubscriptionController;
use App\Http\Controllers\Dashboard\AdminSubscriptionController;
use App\Http\Controllers\Dashboard\AppointmentController;
use App\Http\Controllers\Dashboard\FoodController;
use App\Http\Controllers\Dashboard\LessonController;
use App\Http\Controllers\Dashboard\LessonTrainerController;
use App\Http\Controllers\Dashboard\LessonTypeController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\TestimonialController;
use App\Http\Controllers\Dashboard\TrainerController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

// Public route
Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);
Route::post('/v1/refresh', [AuthController::class, 'refresh']);
Route::post('/v1/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('v1/forget-password', [ForgetPasswordController::class, 'sendOtp']);
Route::post('v1/resend-otp', [ForgetPasswordController::class, 'resendOtp']);
Route::post('v1/verify-otp', [ForgetPasswordController::class, 'verifyOtp']);
Route::post('v1/reset-password', [ForgetPasswordController::class, 'resetPassword']);
Route::post('v1/verify-email-otp', [VerifyEmailController::class, 'sendEmailVerifyOtp']);
Route::post('v1/verify-email', [VerifyEmailController::class, 'verifyEmail']);

Route::get('v1/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);

Route::get('v1/auth/{provider}/callback', [SocialLoginController::class, 'callback']);

Route::prefix('v1/')->group(function () {
    // Admin route only
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        // trainer route
        Route::apiResource('trainers', TrainerController::class);

        // role route
        Route::get('roles', [RoleController::class, 'index']);
    });

    // Admin & Trainer route
    Route::middleware(['auth:sanctum', 'role:admin,trainer'])->group(function () {
        // payment route
        Route::resource('payments', PaymentController::class);

        // appointment route
        Route::apiResource('appointments', AppointmentController::class)->only('index', 'update', 'destroy');

        // subscription
        Route::resource('subscriptions', SubscriptionController::class)->only('store', 'update', 'destroy');

        // subscription user
        Route::resource('subscription-users', AdminSubscriptionController::class);

        // lesson type route
        Route::resource('lesson-types', LessonTypeController::class);

        // lessontrainer route
        Route::post('lesson-trainers', [LessonTrainerController::class, 'assign']);
        Route::delete('lesson-trainers/{id}', [LessonTrainerController::class, 'unassign']);

        // lesson route
        Route::resource('lessons', LessonController::class)->only('update', 'destroy', 'store');

        // food route
        Route::resource('foods', FoodController::class);

        // testimonials route
        Route::apiResource('testimonials', TestimonialController::class)->only('destroy');
    });

    // Admin Trainer Student
    Route::middleware(['auth:sanctum', 'role:admin,trainer,student'])->group(function () {
        // user route
        Route::apiResource('users', UserController::class);
        // lesson route
        Route::apiResource('lessons', LessonController::class)->only('index', 'show');

        // testimonials route
        Route::apiResource('testimonials', TestimonialController::class)->only('index');

        // subscription
        Route::resource('subscriptions', SubscriptionController::class)->only('index');
    });

    // Student only
    Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
        // subscription route
        Route::get('/users/{id}/subscriptions', [UserSubscriptionController::class, 'index']);
        Route::post('/users/{id}/{subscriptionId}/subscriptions', [UserSubscriptionController::class, 'store']);

        // testimonials route
        Route::post('testimonials/{id}/create', [ClientTestimonialController::class, 'store']);
        Route::delete('testimonials/{id}/{testimonialId}/delete', [ClientTestimonialController::class, 'destroy']);

        // appointment route
        Route::post('users/{id}/appointments/create', [ClientAppointmentController::class, 'create']);
        Route::get('users/{id}/appointments/history', [ClientAppointmentController::class, 'history']);

        // food route
        Route::get('/users/{userId}/foods', [ClientFoodController::class, 'index']);
        Route::get('/users/{userId}/foods/{id}', [ClientFoodController::class, 'show']);
    });
});
