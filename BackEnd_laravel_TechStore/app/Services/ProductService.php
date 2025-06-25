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
        return $this->productRepository->getProductsByPromotionType();
    }
    public function getAllProductsWithImages()
    {
        return $this->productRepository->getAllProductsWithImages();
    }

    public function getTopFiveProducts()
    {
        return $this->productRepository->getTopFiveProducts();
    }
    
}
