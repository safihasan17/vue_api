<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $items = [
            ['type' => 'Consultation Fee', 'description' => 'Doctor consultation fee', 'amount' => [500, 2000]],
            ['type' => 'Medicine', 'description' => 'Pharmacy medicines', 'amount' => [100, 1500]],
            ['type' => 'Lab Test', 'description' => 'Laboratory test charge', 'amount' => [300, 3000]],
            ['type' => 'Bed Charge', 'description' => 'Ward bed charge (per day)', 'amount' => [800, 5000]],
            ['type' => 'Other', 'description' => 'Miscellaneous hospital charge', 'amount' => [50, 500]],
        ];

        $item = $this->faker->randomElement($items);

        return [
            'invoice_id' => Invoice::inRandomOrder()->value('id') ?? Invoice::factory(),
            'item_type' => $item['type'],
            'item_reference_id' => null,
            'description' => $item['description'],
            'amount' => $this->faker->randomFloat(2, $item['amount'][0], $item['amount'][1]),
        ];
    }
}
