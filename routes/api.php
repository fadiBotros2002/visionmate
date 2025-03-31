<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Middleware\BlindMiddleware;
use App\Http\Middleware\VolunteerMiddleware;
use App\Http\Controllers\auth\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RatingController;


/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', BlindMiddleware::class])->group(function () {

    // Blind user creates request
    Route::post('/request', [RequestController::class, 'store']);
    // Blind rate the volunteer
    Route::post('/rate_volunteer', [RatingController::class, 'rateVolunteer']);

});


Route::middleware(['auth:sanctum', VolunteerMiddleware::class])->group(function () {

    // Route for updating user info
    Route::get('/user_info', [ProfileController::class, 'viewProfile']);

    // Route for updating user info
    Route::put('/update_info', [ProfileController::class, 'updateUserInfo']);

    // Volunteer gets notifications
    Route::get('/notifications', [RequestController::class, 'notifications']);


    // download certificate
Route::get('/download_certificate', [RatingController::class, 'downloadCertificate']);


// Volunteer handles notification click: marks as read & accepts request
Route::post('/notifications/{notification_id}/handle', [RequestController::class, 'handleNotificationClick']);

});
