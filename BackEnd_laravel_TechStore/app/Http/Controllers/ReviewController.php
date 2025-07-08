<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

    // public function storeReview(Request $request, $productId)
    // {
    //     $validated = $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'comment' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     $user = Auth::guard('user')->user();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     // Gọi Service xử lý tạo review
    //     $this->reviewService->createReview($user->id, $productId, $validated);

    //     return response()->json(['message' => 'Review submitted successfully.'], 201);
    // }

    // public function storeReview(Request $request, $productId)
    // {
    //     $validated = $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'comment' => 'nullable|string',
    //         'file' => 'nullable|file|image|max:5120',
    //     ]);

    //     $user = Auth::guard('user')->user();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     // Xử lý upload ảnh tại đây (nếu có)
    //     $filePath = null;
    //     if ($request->hasFile('file') && $request->file('image')->isValid()) {
    //         $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
    //             'folder' => 'Reviews',
    //         ]);
    //         $imageUrl = $uploadedFile->getSecurePath();
    //     }

    //     // Gọi Service (chỉ xử lý dữ liệu)
    //     $this->reviewService->createReview($user->id, $productId, [
    //         'rating' => $validated['rating'],
    //         'comment' => $validated['comment'] ?? '',
    //         'image_url' => $imageUrl,
    //     ]);

    //     return response()->json(['message' => 'Review submitted successfully.'], 201);
    // }

    // public function storeReview(Request $request, $productId)
    // {
    //     $validated = $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'comment' => 'nullable|string',
    //         'file' => 'nullable|file|image|max:5120',
    //     ]);

    //     $user = Auth::guard('user')->user();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not authenticated'], 401);
    //     }

    //     // Upload ảnh nếu có
    //     $imageUrl = null;
    //     if ($request->hasFile('file') && $request->file('file')->isValid()) {
    //         $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
    //             'folder' => 'Reviews',
    //         ]);
    //         $imageUrl = $uploadedFile->getSecurePath();
    //     }

    //     // Gọi service xử lý lưu review
    //     $this->reviewService->createReview($user->id, $productId, [
    //         'rating' => $validated['rating'],
    //         'comment' => $validated['comment'] ?? '',
    //         'image_url' => $imageUrl,
    //     ]);

    //     return response()->json(['message' => 'Review submitted successfully.'], 201);
    // }

    public function storeReview(Request $request, $productId)
{
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string',
        'file' => 'nullable|file|image|max:5120',
    ]);

    $user = Auth::guard('user')->user();
    if (!$user) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    // Upload ảnh nếu có
    $imageUrl = null;
    if ($request->hasFile('file') && $request->file('file')->isValid()) {
        try {
            $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => 'Reviews',
                'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'techstore'),
            ]);
            $imageUrl = $uploadedFile->getSecurePath();
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể tải ảnh lên Cloudinary'], 500);
        }
    }

    // Gọi service xử lý lưu review
    $this->reviewService->createReview($user->id, $productId, [
        'rating' => $validated['rating'],
        'comment' => $validated['comment'] ?? '',
        'image_url' => $imageUrl,
    ]);

    return response()->json(['message' => 'Đánh giá đã được gửi thành công.'], 201);
}

}
