<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicinePurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
             'supplier_id' => Supplier::inRandomOrder()->value('id') ?? Supplier::factory(),
            'medicine_id' => Medicine::inRandomOrder()->value('id') ?? Medicine::factory(),
            'quantity' => fake()->numberBetween(10, 200),
            'purchase_price' => fake()->randomFloat(2, 1, 100),
            'purchase_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
