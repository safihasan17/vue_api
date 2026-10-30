<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'patient_id'        => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_id'         => Doctor::inRandomOrder()->value('id') ?? Doctor::factory(),
            'appointment_date'  => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'appointment_time'  => $this->faker->time('H:i:s'),
            'status'            => $this->faker->randomElement(['Scheduled', 'Checked-in', 'Waiting', 'Completed', 'Cancelled']),
            'reason'            => $this->faker->randomElement([
                'Routine checkup',
                'Follow-up visit',
                'Fever & cough',
                'Chest pain follow-up',
                'Knee pain',
                'Migraine',
                'Skin allergy',
                'Blood pressure check',
            ]),
        ];
    }

    /**
     * Indicate that the appointment is scheduled for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'appointment_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the appointment has been completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Completed',
        ]);
    }

    /**
     * Indicate that the appointment has been cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Cancelled',
        ]);
    }
}