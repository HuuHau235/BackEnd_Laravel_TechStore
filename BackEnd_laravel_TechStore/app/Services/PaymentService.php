<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Models\Order;
use Illuminate\Support\Carbon;

class PaymentService
{
    protected $paymentRepo;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
    }

    public function confirmPayment($data)
    {
       $payment = $this->paymentRepo->create([
            'order_id'     => $data['order_id'],
            'method'       => $data['method'],
            'status'       => 'Completed',
            'payment_date' => Carbon::now(),
        ]);

        return $payment;

    }
}
