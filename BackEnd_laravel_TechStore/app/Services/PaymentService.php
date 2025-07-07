<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
<<<<<<< HEAD
use App\Repositories\PaymentRepository;use Illuminate\Support\Carbon;
=======
use App\Repositories\PaymentRepository;
use Illuminate\Support\Carbon;
>>>>>>> bb78eb9e3ab2aa96b6eda5c9eb707a901967165f

class PaymentService
{
    protected $paymentRepo;
    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
    }
    public function createPayment(array $data)
    {
        $paymentMethod = $data['payment_method'];

        $status = ($paymentMethod === 'cash') ? 'processing' : 'Completed';

        $order = Order::findOrFail($data['order_id']);
        $order->status = $status;
        $order->save();
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $paymentMethod,
            'amount' => $data['amount'],
            'status' => $status,
        ]);

        return $payment;
    }
    public function confirmPayment($data)
    {
<<<<<<< HEAD
       $payment = $this->paymentRepo->create([
            'order_id'     => $data['order_id'],
            'method'       => $data['method'],
            'status'       => 'Completed',
=======
        $payment = $this->paymentRepo->create([
            'order_id' => $data['order_id'],
            'method' => $data['method'],
            'status' => 'Completed',
>>>>>>> bb78eb9e3ab2aa96b6eda5c9eb707a901967165f
            'payment_date' => Carbon::now(),
        ]);

        return $payment;

    }
}
