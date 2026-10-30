<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    public function definition(): array
    {
        $medicines = [
            ['name' => 'Atorvastatin', 'generic_name' => 'Atorvastatin Calcium', 'category' => 'Cardiovascular'],
            ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic Acid', 'category' => 'Cardiovascular'],
            ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'category' => 'Analgesic'],
            ['name' => 'Paracetamol Syrup', 'generic_name' => 'Paracetamol', 'category' => 'Analgesic'],
            ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine HCl', 'category' => 'Antihistamine'],
            ['name' => 'Metoprolol', 'generic_name' => 'Metoprolol Tartrate', 'category' => 'Cardiovascular'],
            ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'category' => 'Gastrointestinal'],
            ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin Trihydrate', 'category' => 'Antibiotic'],
            ['name' => 'Metformin', 'generic_name' => 'Metformin HCl', 'category' => 'Antidiabetic'],
            ['name' => 'Losartan', 'generic_name' => 'Losartan Potassium', 'category' => 'Cardiovascular'],
        ];

        $item = $this->faker->randomElement($medicines);

        return [
            'name' => $item['name'],
            'generic_name' => $item['generic_name'],
            'category' => $item['category'],
            'unit_price' => $this->faker->randomFloat(2, 5, 500),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
        ];
    }
}