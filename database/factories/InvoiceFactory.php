<?php

namespace Database\Factories;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isAdmitted = $this->faker->boolean(40);
        $totalAmount = $this->faker->randomFloat(2, 500, 15000);

        $status = $this->faker->randomElement(['Unpaid', 'Partially Paid', 'Paid', 'Cancelled']);
        $paidAmount = match ($status) {
            'Paid' => $totalAmount,
            'Partially Paid' => round($totalAmount * $this->faker->randomFloat(2, 0.2, 0.8), 2),
            default => 0,
        };

        return [
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            // If "admitted" (IPD), link an admission and leave appointment null.
            // Otherwise (OPD), link an appointment and leave admission null.
            'admission_id' => $isAdmitted
                ? Admission::inRandomOrder()->value('id')
                : null,
            'appointment_id' => ! $isAdmitted
                ? Appointment::inRandomOrder()->value('id')
                : null,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'status' => $status,
            'invoice_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }
}