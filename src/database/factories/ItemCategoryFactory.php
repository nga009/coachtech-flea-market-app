<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Category;

class ItemCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = Category::factory()->create();
        
        return [
            'category_id' => User::factory(),
            'item_id' => $item->id,
        ];
    }
}
