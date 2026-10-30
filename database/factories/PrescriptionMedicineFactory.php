<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionMedicine>
 */
class PrescriptionMedicineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::inRandomOrder()->value('id') ?? Prescription::factory(),
            'medicine_id' => Medicine::inRandomOrder()->value('id') ?? Medicine::factory(),
            'dosage' => $this->faker->randomElement(['500mg 2x/day', '1 tablet at night', '10mg once daily', '5ml 3x/day']),
            'duration' => $this->faker->randomElement(['5 days', '7 days', '14 days', '30 days']),
            'instructions' => $this->faker->optional()->randomElement([
                'Take after meal', 'Take before breakfast', 'Avoid alcohol', 'Complete the full course',
            ]),
        ];
    }
}