<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        'message' => 'Sản phẩm "' . ($product->name ?? 'Không tồn tại') . '" đã hết hàng hoặc không đủ số lượng.',
                        'product_id' => $item['product_id']
                    ], 400);
                }

                // Nếu sản phẩm hết hàng thì không cho tiến hành checkout
                if ($product->stock == 0) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Sản phẩm "' . $product->name . '" đã hết hàng và không thể thanh toán.',
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
}
