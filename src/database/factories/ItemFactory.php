<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition():array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand_name' => $this->faker->company(),
            'item_image' => 'images/sample.png',
            'price' => $this->faker->numberBetween(1000, 10000),
            'description' => $this->faker->sentence(),
            'is_sold' => false,
            'condition' => '新品',
        ];
    }
}
