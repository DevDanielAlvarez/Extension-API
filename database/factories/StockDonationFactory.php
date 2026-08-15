<?php

namespace Database\Factories;

use App\Enums\StockDonationStatusEnum;
use App\Models\Patient;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockDonation>
 */
class StockDonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_item_id' => StockItem::factory(),
            'patient_id' => Patient::factory(),
            'quantity' => fake()->numberBetween(1, 3),
            'donor_name' => fake()->name(),
            'donor_phone' => fake()->optional()->numerify('119########'),
            'notes' => fake()->optional()->sentence(),
            'status' => StockDonationStatusEnum::PENDING,
        ];
    }
}
