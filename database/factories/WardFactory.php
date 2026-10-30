<?php

namespace Database\Factories;

use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ward>
 */
class WardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Ward ' . fake()->unique()->bothify('?##'),
            'floor' => fake()->randomElement(['1st Floor', '2nd Floor', '3rd Floor', '4th Floor']),
            'type' => fake()->randomElement(['General', 'Cabin', 'Critical Care', 'Pediatric']),
        ];
    }
}