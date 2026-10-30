<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed>
 */
class BedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ward_id' => Ward::inRandomOrder()->value('id') ?? Ward::factory(),
            'bed_number' => strtoupper($this->faker->bothify('B-##')),
            'status' => $this->faker->randomElement(['Available', 'Occupied', 'Reserved', 'Maintenance']),
        ];
    }
}