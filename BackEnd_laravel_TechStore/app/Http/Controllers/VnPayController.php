<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $data = $request->all();

        $vnp_TmnCode = "DNQYTRZN"; // Mã website
        $vnp_HashSecret = "T9H53Q0QJFY7Y936K2B7POPCVOWVQLRD"; // Chuỗi bí mật
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8000/api/user/vnpay/return";

        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            return response()->json(['message' => 'Invalid amount'], 400);
        }

        $vnp_TxnRef = uniqid();
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $vnp_TxnRef;

        $vnp_Amount = (int)$data['amount']; // ✅ KHÔNG nhân *100 nữa
        $vnp_IpAddr = $request->ip();
        $vnp_CreateDate = date('YmdHis');
        $vnp_Locale = 'vn';

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        return response()->json(['url' => $vnp_Url]);
    }

    public function handleReturn(Request $request)
    {
        $vnp_HashSecret = "T9H53Q0QJFY7Y936K2B7POPCVOWVQLRD";

        $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($inputData);

        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

        if (strtolower($secureHash) === strtolower($request->vnp_SecureHash)) {
            $status = $request->vnp_ResponseCode === '00' ? 'success' : 'failed';
            return redirect("http://localhost:3000/payment-result?status=$status");
        }

        return redirect("http://localhost:3000/payment-result?status=invalid-signature");
    }
}
