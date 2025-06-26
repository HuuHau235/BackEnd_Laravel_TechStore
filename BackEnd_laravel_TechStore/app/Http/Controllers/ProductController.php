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
public function index()
{
    try {
        $products = $this->productService->getAll();

      return response()->json([
    'status' => true,
    'data' => $products,
]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error fetching products',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function getPromotedProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getPromotedProducts();
            // return response()->json($products, 200);
            return response()->json([
            'status' => true,
            'data' => $products->values(), 
        ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching promoted products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
public function getProductCategories(): JsonResponse
    {
        try {
            $categories = $this->productService->getCategoriesFromProducts();

            return response()->json([
                'status' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching categories from products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
