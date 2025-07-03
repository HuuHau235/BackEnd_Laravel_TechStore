<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;

class OrderRepository
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update($orderId, array $data)
    {
        $order = Order::findOrFail($orderId);
        $order->update($data);
        return $order;
    }

    public function getLatestOrderByUser($userId)
    {
        $order = Order::with('user')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->firstOrFail();

        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            throw new \Exception("Payment not found for order ID: {$order->id}");
        }

        $details = OrderDetail::with('product.firstImage')
            ->where('order_id', $order->id)
            ->get();

        $subtotal = $details->sum(fn($item) => $item->unit_price * $item->quantity);

        $shippingFee = match ($order->shipping_option) {
            'free' => 0,
            'local' => 5,
            'flat' => 15,
            default => 0,
        };

        $discount = $order->discount ?? 0;
        $total = $subtotal + $shippingFee - $discount;

        return [
            'order_code' => '#ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'customer' => [
                'fullname' => $order->full_name,
                'phone' => $order->phone,
                'email' => $order->user->email ?? '',
                'address' => "{$order->address} - {$order->ward} - {$order->district} - {$order->province}",
            ],
            'payment' => [
                'method' => $payment->method,
                'status' => $payment->status,
            ],
            'items' => $details->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'color' => $item->color,
                    'image' => optional($item->product->firstImage)->image_url,
                ];
            }),
            'summary' => [
                'subtotal' => number_format($subtotal, 2),
                'shipping_fee' => number_format($shippingFee, 2),
                'discount' => number_format($discount, 2),
                'total' => number_format($total, 2),
            ]
        ];
    }

}
