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

        $items = $request->selected_items; // array of {product_id, cart_item_id, quantity, unit_price}
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

            foreach ($items as $item) {
                $this->productRepository->createOrderDetail($order->id, $item);
                $this->productRepository->decrementStock($item['product_id'], $item['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to place order', 'error' => $e->getMessage()], 500);
        }
    }

}
