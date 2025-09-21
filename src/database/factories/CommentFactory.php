<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class CommentFactory extends Factory
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
            'user_id' => User::factory(),
            'comment' => $this->faker->words(rand(1, 3), true),
        ];
    }
}
