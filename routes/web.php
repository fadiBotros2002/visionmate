<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\PaymentController;

Route::get('/donate', [PaymentController::class, 'donate'])->name('donation.form');
Route::post('/donate', [PaymentController::class, 'processDonation'])->name('donation.process');

