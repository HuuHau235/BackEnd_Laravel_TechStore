<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getProductsByCategory()
    {
        return Product::select('id', 'name', 'price', 'category_id')
            ->where('status', 'active')
            ->orderBy('category_id')
            ->limit(12)
            ->get()
            ->groupBy('category_id');
    }
}

