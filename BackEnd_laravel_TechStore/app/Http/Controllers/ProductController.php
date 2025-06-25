<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getPromotedProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getPromotedProducts();
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching promoted products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getItemInProductCartByUserId(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $userId = $user->id;

            $cartItems = $this->productService->getUserCart($userId);

            return response()->json([
                'data' => $cartItems
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request, $cartId)
    {
        try {
            $quantity = $request->input('quantity');

            $this->productService->updateCartItemQuantity($cartId, $quantity);

            return response()->json(['message' => 'Cart quantity updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function removeCartItem($id)
    {
        $this->productService->deleteCartItem($id);
        return response()->json(['message' => 'Removed']);
    }

    public function emptyCart(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $this->productService->clearUserCart($user->id);

            return response()->json(['message' => 'Cart cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $coupon = $this->productService->applyCoupon($request->code);

        if (!$coupon) {
            return response()->json(['message' => 'Invalid or expired coupon.'], 400);
        }

        return response()->json(['coupon' => $coupon], 200);
    }

    public function checkout(Request $request)
    {
        return $this->productService->processCheckout($request);
    }
}


