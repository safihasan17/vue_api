<?php

namespace Database\Factories;

use App\Models\LabTest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabTest>
 */
class LabTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tests = [
            ['test_name' => 'Complete Blood Count (CBC)', 'category' => 'Hematology'],
            ['test_name' => 'Blood Glucose (Fasting)', 'category' => 'Biochemistry'],
            ['test_name' => 'Lipid Profile', 'category' => 'Biochemistry'],
            ['test_name' => 'Liver Function Test (LFT)', 'category' => 'Biochemistry'],
            ['test_name' => 'Kidney Function Test (KFT)', 'category' => 'Biochemistry'],
            ['test_name' => 'Urine Routine Examination', 'category' => 'Pathology'],
            ['test_name' => 'Thyroid Profile (T3, T4, TSH)', 'category' => 'Endocrinology'],
            ['test_name' => 'X-Ray Chest', 'category' => 'Radiology'],
            ['test_name' => 'ECG', 'category' => 'Cardiology'],
            ['test_name' => 'Ultrasound Abdomen', 'category' => 'Radiology'],
            ['test_name' => 'HbA1c', 'category' => 'Biochemistry'],
            ['test_name' => 'Widal Test', 'category' => 'Microbiology'],
        ];

        $item = $this->faker->randomElement($tests);

        return [
            'test_name' => $item['test_name'],
            'category' => $item['category'],
            'price' => $this->faker->randomFloat(2, 100, 3000),
        ];
    }
}