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

//    public function createReview($userId, $productId, $data)
//     {
//         $imageUrl = null;

//         // if (!empty($data['image'])) {
//         //     // Upload ảnh lên Cloudinary
//         //     $uploadedFile = Cloudinary::upload($data['image']->getRealPath(), [
//         //         'folder' => 'techstore/reviews',
//         //     ]);

//         //     $imageUrl = $uploadedFile->getSecurePath(); // Lấy URL ảnh
//         // }

//         if (isset($data['image']) && $data['image']->isValid()) {
//             $uploadedFile = Cloudinary::upload($data['image']->getRealPath(), [
//                 'folder' => 'user_uploads',
//             ]);

//             $imageUrl = $uploadedFile->getSecurePath();
//         }

//         return $this->reviewRepo->create([
//             'user_id' => $userId,
//             'product_id' => $productId,
//             'rating' => $data['rating'],
//             'comment' => $data['comment'] ?? '',
//             'image_url' => $imageUrl,
//             'review_date' => now(),
//         ]);
//     }

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
