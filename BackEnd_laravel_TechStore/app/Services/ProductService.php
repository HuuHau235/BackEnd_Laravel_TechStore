<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCart;
use App\Exceptions\OutOfStockException;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getPromotedProducts()
    {
        return $this->productRepository->getProductsByPromotionType();
    }

    public function getAll()
    {
        return $this->productRepository->getAllWithImages();
    }
    public function getCategoriesFromProducts()
    {
        return $this->productRepository->getProductCategories();
    }
    public function getUserCart($userId)
    {
        return $this->productRepository->getCartItemsByUser($userId);
    }

    public function updateCartItemQuantity($cartId, $quantity)
    {
        $cartItem = $this->productRepository->findWithProduct($cartId);

        if (!$cartItem) {
            throw new \Exception('Cart item not found.');
        }

        $stock = $cartItem->product->stock;

        if ($quantity > $stock) {
            throw new \Exception("Requested quantity ($quantity) exceeds available stock ($stock).");
        }

        return $this->productRepository->updateQuantity($cartId, $quantity);
    }

    public function deleteCartItem($id)
    {
        return $this->productRepository->deleteCartItem($id);
    }

    public function clearUserCart($userId)
    {
        return $this->productRepository->deleteCartItemsByUserId($userId);
    }

    public function applyCoupon(string $code)
    {
        return $this->productRepository->getValidCoupon($code);
    }

    public function processCheckout($request)
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $userId = $user->id;

        $items = $request->selected_items;
        if (!is_array($items) || empty($items)) {
            return response()->json(['message' => 'No items selected for checkout'], 400);
        }

        $shippingOption = $request->shipping_option;
        $couponCode = $request->coupon_code;
        $discount = $request->discount ?? 0;
        $total = $request->total_amount;

        DB::beginTransaction();

        try {
            $order = $this->productRepository->createOrder([
                'user_id' => $userId,
                'order_date' => now(),
                'status' => 'pending',
                'shipping_option' => $shippingOption,
                'total_amount' => $total,
                'coupon_code' => $couponCode,
                'discount' => $discount,
            ]);

            $cartItemIdsToDelete = [];

            foreach ($items as $item) {
                $product = $this->productRepository->findProductById($item['product_id']);

                if (!$product || $product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'The product "' . ($product->name ?? 'Not available') . '" is out of stock or does not have sufficient quantity.',
                        'product_id' => $item['product_id']
                    ], 400);
                }

                // Nếu sản phẩm hết hàng thì không cho tiến hành checkout
                if ($product->stock == 0) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'The product "' . $product->name . '" is out of stock and cannot be purchased.',
                        'product_id' => $item['product_id']
                    ], 400);
                }

                $this->productRepository->createOrderDetail($order->id, $item);
                $this->productRepository->decrementStock($item['product_id'], $item['quantity']);
                $cartItemIdsToDelete[] = $item['cart_item_id'];
            }

            // Xoá các sản phẩm đã thanh toán khỏi bảng product_cart
            if (!empty($cartItemIdsToDelete)) {
                $this->productRepository->deleteCartItems($userId, $cartItemIdsToDelete);
            }

            DB::commit();
            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function addToCart($userId, $productId, $quantity)
    {
        $product = $this->productRepository->findProductById($productId);

        if (!$product) {
            throw new \Exception('Product not found.');
        }

        if ($quantity > $product->stock) {
            throw new \Exception('Requested quantity exceeds available stock.');
        }

        return $this->productRepository->addOrUpdateCart($userId, $productId, $quantity);
    }

    public function getAllProductsWithImages()
    {
        return $this->productRepository->getAllProductsWithImages();
    }

    public function getTopFiveProducts()
    {
        return $this->productRepository->getTopFiveProducts();
    }

    public function getProductDetailById(int $productId) {
        return $this->productRepository->getProductWithImagesAndColors($productId);
    }

    public function getProductById($productId)
    {
        return $this->productRepository->find($productId);
    }
    
    public function getRelatedProductsByCategory($categoryId, $excludeProductId)
    {
        return $this->productRepository->getRelatedProducts($categoryId, $excludeProductId);
    }

    public function addProductToCart(int $userId, int $productId, int $quantity, ?string $color)
    {
        // 1. Tìm sản phẩm
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        // 2. Tính tổng số lượng đã có trong cart (mọi màu)
        $cartItemsSameProduct = ProductCart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->get();

        $alreadyInCart = $cartItemsSameProduct->sum('quantity');
        $totalQuantity = $alreadyInCart + $quantity;

        // 3. Nếu vượt stock => báo lỗi
        if ($totalQuantity > $product->stock) {
            throw new OutOfStockException(
                'Requested quantity exceeds available stock',
                $product->stock,
                $alreadyInCart
            );
        }

        // 4. Nếu hợp lệ, cập nhật riêng theo màu
        return $this->productRepository->addToCart($userId, $productId, $quantity, $color);
    }

    public function addProductToWishlist(int $userId, int $productId, ?string $color) {
        return $this->productRepository->addToWishlist($userId, $productId, $color);
    }

    public function removeFromWishlist(int $userId, int $productId, ?string $color)
    {
        return $this->productRepository->removeFromWishlist($userId, $productId, $color);
    }

    // public function processBuyNow(int $userId, int $productId, int $quantity, ?string $color) {
    //     return $this->productRepository->createOrderImmediately($userId, $productId, $quantity, $color);
    // }

    public function processBuyNow(int $userId, int $productId, int $quantity, ?string $color)
    {
        $product = $this->productRepository->findProductById($productId);

        if (!$product) {
            throw new \Exception('Product not found.');
        }

        if ($product->stock <= 0) {
            throw new \Exception('The product is out of stock.');
        }

        if ($quantity > $product->stock) {
            throw new \Exception("Only {$product->stock} item(s) left in stock.");
        }

        // Trừ tồn kho
        $product->stock -= $quantity;
        $product->save();

        // Gọi Repository để tạo đơn hàng tạm thời
        return $this->productRepository->createOrderImmediately($userId, $productId, $quantity, $color);
    }
}
