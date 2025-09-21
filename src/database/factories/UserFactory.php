<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Profile;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

   /**
     * プロフィール付きのユーザー（デフォルトでプロフィールも作成）
     */
    public function withProfile(): static
    {
        return $this->afterCreating(function ($user) {
            Profile::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }

   /**
     * 特定のプロフィール情報付きユーザー
     */
    public function withProfileData(array $profileData = []): static
    {
        return $this->afterCreating(function ($user) use ($profileData) {
            $defaultProfileData = [
                'name' => $this->faker->name(),
                'address' => $this->faker->address(),
            ];

            Profile::factory()->create(array_merge([
                'user_id' => $user->id,
            ], $defaultProfileData, $profileData));
        });
    }

    /**
     * プロフィール未完了のユーザー（プロフィール作成しない）
     */
    public function withoutProfile(): static
    {
        return $this->state([]);  // プロフィールを作成しない
    }
}
