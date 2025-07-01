<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {
        try {
            $products = $this->productService->getAll();

            return response()->json([
                'status' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPromotedProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getPromotedProducts();
            // return response()->json($products, 200);
            return response()->json([
                'status' => true,
                'data' => $products->values(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching promoted products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getProductCategories(): JsonResponse
    {
        try {
            $categories = $this->productService->getCategoriesFromProducts();

            return response()->json([
                'status' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching categories from products',
            ]);
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

    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::guard('user')->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $cartItem = $this->productService->addToCart(
                $user->id,
                $request->product_id,
                $request->quantity
            );

            return response()->json([
                'message' => 'Product added to cart successfully.',
                'data' => $cartItem
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllProduct(Request $request): JsonResponse
    {
        try {
            $categoryId = $request->query('category_id');
            $perPage = $request->query('per_page', 15);

            $query = Product::query();

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $products = $query->with('images')->paginate($perPage);

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTopFiveProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getTopFiveProducts();
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProductByKeyWord(Request $request): JsonResponse
    {
        $keyword = $request->query('q');

        $products = Product::where('name', 'like', "%$keyword%")
            ->with('images')
            ->get();

        return response()->json($products);
    }
}
