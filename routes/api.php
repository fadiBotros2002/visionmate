<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Middleware\BlindMiddleware;
use App\Http\Middleware\VolunteerMiddleware;
use App\Http\Controllers\auth\ProfileController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', BlindMiddleware::class])->group(function () {


});


Route::middleware(['auth:sanctum', VolunteerMiddleware::class])->group(function () {

 // Route for updating user info
 Route::put('/update_info', [ProfileController::class, 'updateUserInfo']);
});
