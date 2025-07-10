<?php
namespace App\Services;

use App\Repositories\ReviewRepository;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReviewService
{
    protected $reviewRepo;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
    }

    public function getReviewsByProductId($productId)
    {
        return $this->reviewRepo->getByProductId($productId);
    }

    public function createReview($userId, $productId, $data)
    {
        return $this->reviewRepo->create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? '',
            'image_url' => $data['image_url'] ?? null,
            'review_date' => now(),
        ]);
    }
}
