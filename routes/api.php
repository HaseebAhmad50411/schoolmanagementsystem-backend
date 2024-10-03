<?php

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentImageController;
use App\Http\Controllers\TeacherImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('user', UserController::class);
    Route::post('subjects/{id}/associate', [SubjectController::class, 'associate']);
    Route::post('courses/{id}/associate', [CourseController::class, 'associate']);
    Route::post('teachers/{id}/associate', [TeacherController::class, 'associate']);
    Route::post('students/{id}/associate', [StudentController::class, 'associate']);
    Route::post('storeImage', [StudentController::class, 'storeImage'] );
    Route::post('images', [ImageController::class, 'store']);
    Route::get('images', [ImageController::class, 'index']);

});

