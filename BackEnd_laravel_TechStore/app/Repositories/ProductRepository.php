<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getProductsByPromotionType()
    {
        $hotProducts = Product::where('promotion_type', 'hot')
            ->with('images') 
            ->take(3)
            ->get();

        $newProducts = Product::where('promotion_type', 'new')
            ->with('images')
            ->take(3)
            ->get();

        $summerSaleProducts = Product::where('promotion_type', 'summer sale')
            ->with('images')
            ->take(3)
            ->get();

        $bestDealProducts = Product::where('promotion_type', 'best deal')
            ->with('images')
            ->take(3)
            ->get();

        return $hotProducts
            ->merge($newProducts)
            ->merge($summerSaleProducts)
            ->merge($bestDealProducts);
    }

    public function getAllProductsWithImages()
    {
        return Product::with('images')->get();
    }

    public function getTopFiveProducts()
    {
        return Product::with('images') 
            ->orderBy('created_at', 'desc') 
            ->take(5)
            ->get();
    }
}
