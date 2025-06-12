<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getPromotedProductsByTypes(array $types, int $limitPerType = 4)
    {
        $result = [];

        foreach ($types as $type) {
            $products = Product::where('promotion_type', $type)
                ->limit($limitPerType)
                ->get();
            $result[$type] = $products;
        }

        return $result;
    }
}

