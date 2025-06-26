<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getProductsByPromotionType()
    {
        $hotProducts = $this->getProductsByType('hot', 3);
        $newProducts = $this->getProductsByType('new', 3);
        $summerSaleProducts = $this->getProductsByType('summer sale', 3);
        $bestDealProducts = $this->getProductsByType('best deal', 3);
        $featureProducts = $this->getFeaturedProducts(['featured product'], 10);


        return $hotProducts
            ->merge($newProducts)
            ->merge($summerSaleProducts)
            ->merge($bestDealProducts)
            ->merge($featureProducts);
    }

    public function getProductsByType($type, $limit = 3)
    {
        return Product::where('promotion_type', $type)
            ->with(['images', 'reviews'])
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }


    public function getFeaturedProducts(array $types = ['featured product', 'best deal'], $limit = 10)
    {
        return Product::with(['images', 'category', 'reviews'])
            ->whereIn('promotion_type', $types)
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }
    public function getAllWithImages()
    {
        return Product::with('images')->get()->transform(function ($product) {
            $product->image_url = $product->images->first()->image_url ?? null;
            return $product;
        });
    }
    public function getProductCategories()
    {
        return Product::with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->values();
    }
}
