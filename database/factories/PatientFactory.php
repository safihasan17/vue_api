<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'dob' => $this->faker->dateTimeBetween('-80 years', '-1 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'address' => $this->faker->address(),
            'phone' => $this->faker->numerify('01#########'),
            'guardian_name' => $this->faker->optional()->name(),
            'emergency_contact' => $this->faker->numerify('01#########'),
        ];
    }
}