<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/promoted-products', [ProductController::class, 'getPromotedProducts']);
