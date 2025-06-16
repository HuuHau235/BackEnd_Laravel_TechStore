<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getPromotedProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getPromotedProducts();
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching promoted products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
