<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:COD,VNPay,Momo,PayPal,QR'
        ]);

        $result = $this->paymentService->confirmPayment($validated);

        return response()->json([
            'message' => 'Xác nhận thanh toán thành công.',
            'payment_id' => $result->id,
        ]);
    }
}
