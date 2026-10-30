<?php

namespace Database\Factories;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admission>
 */
class AdmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Admitted', 'Discharged', 'Transferred']);
        $admissionDate = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_id' => Doctor::inRandomOrder()->value('id') ?? Doctor::factory(),
            'ward_id' => Ward::inRandomOrder()->value('id') ?? Ward::factory(),
            'bed_id' => Bed::inRandomOrder()->value('id') ?? Bed::factory(),
            'admission_date' => $admissionDate,
            'discharge_date' => $status === 'Discharged'
                ? $this->faker->dateTimeBetween($admissionDate, 'now')
                : null,
            'status' => $status,
        ];
    }
}