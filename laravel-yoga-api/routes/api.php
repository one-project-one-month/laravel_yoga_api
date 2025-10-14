<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTrainerController;

Route::middleware('api')->group(function () {
    Route::get('/lessons', [LessonController::class, 'index']);
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::post('/lessons', [LessonController::class, 'store']);
    Route::put('/lessons/{id}', [LessonController::class, 'update']);
    Route::delete('/lessons/{id}', [LessonController::class, 'destroy']);

    Route::post('/lesson-trainer', [LessonTrainerController::class, 'assign']);
});