<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;


class FavoriteFactory extends Factory
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
            'user_id' => User::factory(),
            'item_id' => $item->id,
        ];
    }
}
