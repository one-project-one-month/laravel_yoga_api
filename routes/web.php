<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTrainerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
Route::get('/users/{id}', function ($id) {
    return 'This is user'.$id;
});
|
*/

Route::get('/', function () {
    return view('welcome');
});





Route::apiResource('lessons', LessonController::class);


// Use a descriptive name for the route group
Route::controller(LessonTrainerController::class)->group(function () {
    // List all trainer-lesson type assignments
    Route::get('lesson-type-assignments', 'index'); 
    
    // Create/Attach a trainer to a lesson type
    Route::post('lesson-type-assignments', 'store'); 
    
    // Delete/Detach a trainer from a lesson type (by pivot ID or body params)
    Route::delete('lesson-type-assignments/{id?}', 'destroy'); 
});


