<?php
namespace App\Repositories;

use App\Models\Review;

class ReviewRepository
{
    public function getByProductId($productId)
    {
        return Review::with('user') // lấy luôn thông tin user
                     ->where('product_id', $productId)
                     ->orderByDesc('review_date')
                     ->get(); 
    }

    public function create(array $data)
    {
        \Log::info('Review Data:', $data);
        return Review::create($data);
    }
}
