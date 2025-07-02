<?php
namespace App\Repositories;

use App\Models\Order;

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

}
