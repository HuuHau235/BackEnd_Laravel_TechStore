<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BlogContronller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Tất cả route ở đây được tự động gắn prefix là /api
*/

// ==============================
//  PUBLIC PRODUCT ROUTES
// ==============================
Route::prefix('products')->group(function () {
    Route::get('/promoted-aboutus', [ProductController::class, 'getPromotedProducts']);
});
// ==============================
// AUTH ROUTES (Blog)
// ==============================
Route::get('/blogs', [BlogContronller::class, 'index']);
Route::get('/blogs/status', [BlogContronller::class, 'getStatusBlog']);


Route::get('/blogs/categories', [CategoryController::class, 'getCategoriesByID']);



// ==============================
// AUTH ROUTES (Public)
// ==============================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verify']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


// ==============================
// AUTH ROUTES (Protected via Sanctum)
// ==============================
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    // Lấy thông tin người dùng đã đăng nhập
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});


// ==============================
// ADMIN ROUTES (Protected)
// ==============================
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Route::get('/dashboard', [AdminController::class, 'getStats']);
    // Minh họa những cái bỏ vào auth:sanctum là đã đăng nhập rồi mới có
});

// ==============================
// USER ROUTES (Protected)
// ==============================
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Route::get('/dashboard', [AdminController::class, 'getStats']);
    // Minh họa những cái bỏ vào auth:sanctum là đã đăng nhập rồi mới có
});


Route::prefix('user')->group(function (){
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::put('/update-profile/{id}', [UserController::class, 'updateProfile']); 
    Route::put('/change-password/{id}', [UserController::class, 'changePassword']); 
    Route::put('/update-avatar/{id}', [UserController::class, 'updateAvatar']);

});