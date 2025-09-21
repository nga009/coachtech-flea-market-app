<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'profile_image' => null,
            'name' => $this->faker->name,
            'postcode' => $this->faker->postcode,
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress
        ];
    }

}
