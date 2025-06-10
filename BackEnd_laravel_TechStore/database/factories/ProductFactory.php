<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'old_price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
            'category_id' => \App\Models\Category::factory(),
            'stock' => $this->faker->numberBetween(0, 100),
            'image_url' => $this->faker->imageUrl(640, 480, 'electronics'),
            'created_at' => now(),
        ];
    }
}
