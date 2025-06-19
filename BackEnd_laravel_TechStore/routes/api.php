<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::prefix('products')->group(function () {
    Route::get('/promoted-aboutus', [ProductController::class, 'getPromotedProducts']);
});

Route::prefix('user')->group(function (){
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::put('/update-profile/{id}', [UserController::class, 'updateProfile']); 
    Route::put('/change-password/{id}', [UserController::class, 'changePassword']); 
    Route::put('/update-avatar/{id}', [UserController::class, 'updateAvatar']);

});
    