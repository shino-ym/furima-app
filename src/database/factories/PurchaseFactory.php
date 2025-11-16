<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;


class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition():array
    {
        return [
        'user_id'=>User::factory(),
        'item_id'=>Item::factory(),
        'payment_method'=> $this->faker->randomElement(['credit_card', 'convenience']),
        'payment_status'=> $this->faker->randomElement(['pending', 'completed', 'failed']),
        'stripe_payment_id'=> $this->faker->uuid(),
        'amount'=> $this->faker->numberBetween(1000, 10000),
        'shipping_postal_code'=>$this->faker->postcode(),
        'shipping_address_line'=> $this->faker->address(),
        'shipping_building'=> $this->faker->optional()->secondaryAddress(),
        ];
    }
}
