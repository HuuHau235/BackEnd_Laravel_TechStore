<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

Route::prefix('products')->group(function () {
    Route::get('/promoted-aboutus', [ProductController::class, 'getPromotedProducts']);
});

Route::post('auth/register', [AuthController::class, 'register']); 
Route::post('auth/verify-otp', [AuthController::class, 'verify']); 
Route::post('auth/login', [AuthController::class, 'login'])->name('login'); 
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
