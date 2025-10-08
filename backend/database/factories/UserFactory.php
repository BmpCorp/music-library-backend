<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            User::NAME => fake()->name(),
            User::EMAIL => fake()->unique()->safeEmail(),
            User::EMAIL_VERIFIED_AT => null,
            User::PASSWORD => 'password',
        ];
    }
}
