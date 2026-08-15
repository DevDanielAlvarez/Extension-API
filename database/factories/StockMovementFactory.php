<?php

namespace Database\Factories;

use App\Enums\StockMovementTypeEnum;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
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
            'type' => StockMovementTypeEnum::IN,
            'quantity' => fake()->numberBetween(1, 50),
            'patient_id' => null,
            'user_id' => User::factory(),
            'batch' => null,
            'expiry_date' => null,
            'notes' => null,
            'movement_date' => now(),
        ];
    }
}
