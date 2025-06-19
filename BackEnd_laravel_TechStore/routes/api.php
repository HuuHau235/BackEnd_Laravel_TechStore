<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BlogContronller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('products')->group(function () {
    Route::get('/promoted-aboutus', [ProductController::class, 'getPromotedProducts']);
});

// Blogs
Route::get('/blogs', [BlogContronller::class, 'index']);
Route::get('/blogs/status', [BlogContronller::class, 'getStatusBlog']);


Route::get('/blogs/categories', [CategoryController::class, 'getCategoriesByID']);