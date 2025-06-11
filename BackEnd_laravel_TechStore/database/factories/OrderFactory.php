<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'order_date' => now(),
            'status' => $this->faker->randomElement(['processing', 'completed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'created_at' => now(),
        ];
    }
}
