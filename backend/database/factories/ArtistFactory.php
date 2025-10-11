<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Artist::TITLE => fake()->company(),
            Artist::DESCRIPTION => fake()->text(),
            Artist::GENRES => fake()->words(3, true),
        ];
    }

    public function withCountry(): static
    {
        $country = Country::inRandomOrder()->first();
        return $this->state(fn (array $attributes) => [
            Artist::COUNTRY_ID => $country->id,
        ]);
    }
}
