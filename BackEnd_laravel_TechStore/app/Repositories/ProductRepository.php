<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;

class ProductRepository
{
    public function getProductsByPromotionType()
    {
        $hotProducts = Product::where('promotion_type', 'hot')
            ->with('images') 
            ->take(3)
            ->get();

        $newProducts = Product::where('promotion_type', 'new')
            ->with('images')
            ->take(3)
            ->get();

        $summerSaleProducts = Product::where('promotion_type', 'summer sale')
            ->with('images')
            ->take(3)
            ->get();

        $bestDealProducts = Product::where('promotion_type', 'best deal')
            ->with('images')
            ->take(3)
            ->get();

        return $hotProducts
            ->merge($newProducts)
            ->merge($summerSaleProducts)
            ->merge($bestDealProducts);
    }

    public function getCartItemsByUser($userId)
    {
        return ProductCart::with('product.firstImage')  /* lấy từ hàm firstImage bên trong Product model */
        ->where('user_id', $userId)
        ->get();
    }

    public function findWithProduct($cartId)
    {
        return ProductCart::with('product')->find($cartId);
    }

    public function updateQuantity($cartId, $quantity)
    {
        return ProductCart::where('id', $cartId)->update(['quantity' => $quantity]);
    }

    public function deleteCartItem($id)
    {
        return ProductCart::where('id', $id)->delete();
    }

    public function deleteCartItemsByUserId($userId)
    {
        return ProductCart::where('user_id', $userId)->delete();
    }

    public function getValidCoupon(string $code)
    {
        $now = Carbon::today();

        return Coupon::where('code', $code)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->first();
    }

    public function createOrder($data)
    {
        return Order::create($data);
    }

    public function createOrderDetail($orderId, $item)
    {
        return OrderDetail::create([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
        ]);
    }

    public function decrementStock($productId, $qty)
    {
        Product::where('id', $productId)->decrement('stock', $qty);
    }

    public function getAllProductsWithImages()
    {
        return Product::with('images')->get();
    }

    public function getTopFiveProducts()
    {
        return Product::with('images') 
            ->orderBy('created_at', 'desc') 
            ->take(5)
            ->get();
    }
}
