<?php
namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService
{
    protected $orderRepo;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function createOrder($userId, $data)
    {
        return $this->orderRepo->create([
            'user_id' => $userId,
            'order_date' => now(),
            'status' => 'pending',
            'shipping_option' => $data['shipping_option'] ?? 'default',
            'total' => $data['total_amount']
        ]);
    }

public function updateOrderInfo($orderId, $data)
{
    return $this->orderRepo->update($orderId, [
        'full_name' => $data['full_name'],
        'phone' => $data['phone'],
        'address' => $data['address'],
        'province' => $data['province'],
        'district' => $data['district'],
        'ward' => $data['ward'],
    ]);
}

}
