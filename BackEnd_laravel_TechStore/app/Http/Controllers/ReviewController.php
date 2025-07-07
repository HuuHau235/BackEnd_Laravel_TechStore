<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getReviewsByProduct($productId): JsonResponse
    {
        $reviews = $this->reviewService->getReviewsByProductId($productId);
        return response()->json([
            'product_id' => (int) $productId,
            'total_reviews' => $reviews->count(),
            'data' => $reviews
        ]);
    }
}
