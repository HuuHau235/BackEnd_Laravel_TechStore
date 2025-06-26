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
        $hotProducts = $this->getProductsByType('hot', 3);
        $newProducts = $this->getProductsByType('new', 3);
        $summerSaleProducts = $this->getProductsByType('summer sale', 3);
        $bestDealProducts = $this->getProductsByType('best deal', 3);
        $featureProducts = $this->getFeaturedProducts(['featured product'], 10);


        return $hotProducts
            ->merge($newProducts)
            ->merge($summerSaleProducts)
            ->merge($bestDealProducts)
            ->merge($featureProducts);
    }

    public function getProductsByType($type, $limit = 3)
    {
        return Product::where('promotion_type', $type)
            ->with(['images', 'reviews'])
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }


    public function getFeaturedProducts(array $types = ['featured product', 'best deal'], $limit = 10)
    {
        return Product::with(['images', 'category', 'reviews'])
            ->whereIn('promotion_type', $types)
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->rating = round($product->reviews->avg('rating'), 1) ?? 0;
                $product->image_url = $product->images->first()->image_url ?? null;
                return $product;
            });
    }
    public function getAllWithImages()
    {
        return Product::with('images')->get()->transform(function ($product) {
            $product->image_url = $product->images->first()->image_url ?? null;
            return $product;
        });
    }
    public function getProductCategories()
    {
        return Product::with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->values();
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
