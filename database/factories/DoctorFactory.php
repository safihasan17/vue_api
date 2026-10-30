<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->doctorUserId(),
            'department_id' => Department::inRandomOrder()->value('id'),
            'specialization' => $this->faker->randomElement([
                'Cardiology', 'Orthopedics', 'Pediatrics', 'Neurology',
                'Gynecology', 'Dermatology', 'ENT', 'General Medicine',
            ]),
            'qualification' => $this->faker->randomElement([
                'MBBS, MD', 'MBBS, MS', 'MBBS, FCPS', 'MBBS, DGO', 'MBBS',
            ]),
            'consultation_fee' => $this->faker->randomFloat(2, 500, 2000),
            'image'=>null,
        ];
    }

    
    protected function doctorUserId(): int
    {
        $doctorRole = Role::firstOrCreate(['name' => 'Doctor']);

        $existingUserId = User::where('role_id', $doctorRole->id)
            ->inRandomOrder()
            ->value('id');

        if ($existingUserId) {
            return $existingUserId;
        }

        return User::factory()->create(['role_id' => $doctorRole->id])->id;
    }
}