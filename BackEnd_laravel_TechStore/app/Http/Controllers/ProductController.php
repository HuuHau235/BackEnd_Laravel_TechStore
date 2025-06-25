<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product; 
use Illuminate\Http\Request;
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

     public function getAllProduct(Request $request): JsonResponse
{
    try {
        $categoryId = $request->query('category_id');
        $perPage = $request->query('per_page', 15);

        $query = Product::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->with('images')->paginate($perPage);

        return response()->json($products, 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching products',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    
    public function getTopFiveProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getTopFiveProducts();
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getProductByKeyWord(Request $request): JsonResponse
{
    $keyword = $request->query('q');

    $products = Product::where('name', 'like', "%$keyword%")
        ->with('images') 
        ->get();

    return response()->json($products);
}

}
