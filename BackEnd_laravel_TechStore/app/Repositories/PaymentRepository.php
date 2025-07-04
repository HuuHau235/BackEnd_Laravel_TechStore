<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function create(array $data)
    {
        return Payment::create($data);
    }
<<<<<<< HEAD
=======
    
>>>>>>> bb78eb9e3ab2aa96b6eda5c9eb707a901967165f
}
