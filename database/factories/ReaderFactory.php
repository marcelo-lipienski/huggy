<?php

namespace Database\Factories;

use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reader>
 */
class ReaderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'birthdate' => fake()->date(),
        ];
    }
}
