<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Album::TITLE => fake()->company(),
            Album::DESCRIPTION => fake()->text(),
            Album::GENRES => fake()->words(3, true),
            Album::YEAR => fake()->year(),
            Album::SONG_COUNT => fake()->numberBetween(4, 16),
            Album::HAS_EXPLICIT_LYRICS => rand(0, 10) < 2,
        ];
    }
}
