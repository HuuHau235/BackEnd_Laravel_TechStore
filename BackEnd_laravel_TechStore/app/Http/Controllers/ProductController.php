<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

class  ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getPromotedProducts()
    {
        $products = $this->productService->getPromotedProducts();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
}

