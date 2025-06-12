<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getPromotedProducts()
    {
        $promotionTypes = ['best deal', 'hot', 'new', 'summer sale']; 
        $productsByType = $this->productRepository->getPromotedProductsByTypes($promotionTypes, 4);

        // Gộp tất cả lại thành 1 array nếu muốn trả về danh sách phẳng
        $flatList = collect($productsByType)->flatten(1);

        return [
            'grouped' => $productsByType,
            'flat' => $flatList
        ];
    }
}


