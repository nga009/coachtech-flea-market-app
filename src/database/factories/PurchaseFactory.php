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
   public function definition()
    {
        $item = Item::factory()->create();
        
        return [
            'item_id' => $item->id,
            'buyer_id' => User::factory(),
            'shipping_postcode' => $this->faker->postcode,
            'shipping_address' => $this->faker->address,
            'shipping_building' => $this->faker->secondaryAddress,
            'payment_method' => $this->faker->numberBetween(1,2),
            'stripe_session_id' => $this->faker->regexify('[A-Za-z0-9]{10}'),
        ];
    }
}
