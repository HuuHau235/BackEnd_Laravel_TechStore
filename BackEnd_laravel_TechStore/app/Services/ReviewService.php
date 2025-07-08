<?php
namespace App\Services;

use App\Repositories\ReviewRepository;

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
}
