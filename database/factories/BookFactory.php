<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'publisher_id' => Publisher::factory(),
            'title' => fake()->text(),
            'genre' => fake()->word(),
            'author' => fake()->name(),
            'year' => (int) fake()->year(),
            'pages' => fake()->numberBetween(10, 999),
            'language' => fake()->word(),
            'edition' => fake()->numberBetween(1, 99),
            'isbn' => fake()->isbn13(),
        ];
    }
}
