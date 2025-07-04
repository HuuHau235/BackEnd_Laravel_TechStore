<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // B1: Tạo đơn hàng sơ khởi
    public function createOrder(Request $request)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $order = $this->orderService->createOrder($user->id, $request->all());

        return response()->json(['message' => 'Order created', 'order_id' => $order->id]);
    }

    // B2: Cập nhật thông tin người mua
    public function updateOrderInfo(Request $request, $orderId)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
        ]);

        $this->orderService->updateOrderInfo($orderId, $validated);

        return response()->json(['message' => 'Customer info updated successfully']);
    }

    public function getConfirmationDetails()
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }


        $data = $this->orderService->getOrderConfirmationDetails($user->id);
        return response()->json($data);
    }

    public function confirmPayment()
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $this->orderService->confirmOrderAndSendMail($user->id);
        return response()->json(['message' => 'Order confirmed and email sent successfully.']);
    }

}
