<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_id' => Doctor::inRandomOrder()->value('id') ?? Doctor::factory(),
            'appointment_id' => Appointment::inRandomOrder()->value('id'),
            'notes' => $this->faker->optional()->randomElement([
                'Review after 1 week if symptoms persist.',
                'Advised complete bed rest for 3 days.',
                'Drink plenty of fluids and avoid oily food.',
                'Follow up if fever continues beyond 3 days.',
            ]),
            'prescription_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }
}