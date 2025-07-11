<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

class ProductCategoryController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getCamera()
    {
        return response()->json($this->productService->getProductsByCategoryId(6));
    }

    public function getSmartWatch()
    {
        return response()->json($this->productService->getProductsByCategoryId(7));
    }

    public function getChargingAccessory()
    {
        return response()->json($this->productService->getProductsByCategoryId(8));
    }

    public function getTV()
    {
        return response()->json($this->productService->getProductsByCategoryId(9));
    }

    public function getAirConditioner()
    {
        return response()->json($this->productService->getProductsByCategoryId(10));
    }
}
