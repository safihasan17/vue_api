<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\LabTest;
use App\Models\LabTestOrder;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabTestOrder>
 */
class LabTestOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'Cancelled']);

        return [
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_id' => Doctor::inRandomOrder()->value('id') ?? Doctor::factory(),
            'test_id' => LabTest::inRandomOrder()->value('id') ?? LabTest::factory(),
            'status' => $status,
            // Only Completed orders have a result filled in
            'result' => $status === 'Completed'
                ? $this->faker->randomElement([
                    'All parameters within normal range.',
                    'Slightly elevated levels, follow-up recommended.',
                    'Results normal, no abnormalities detected.',
                    'Mild deviation noted, correlate clinically.',
                ])
                : null,
            'order_date' => $this->faker->dateTimeBetween('-20 days', 'now')->format('Y-m-d'),
        ];
    }
}