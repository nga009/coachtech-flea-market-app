<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(rand(1, 3), true), // 1-3個の単語で商品名
            'condition' => $this->faker->numberBetween(1,4), // 1から4の数値
            'description' => $this->faker->sentence(rand(5, 15)), // 5-15個の単語で説明文
            'price' => $this->faker->numberBetween(100,99999), // 100円から99,999円の価格
            'item_image' => null, // 画像は一旦null
            'seller_id' => User::factory(), // ユーザーファクトリで関連ユーザーを作成
            'is_sold' => false, // デフォルトは未売却
        ];
    }


}
