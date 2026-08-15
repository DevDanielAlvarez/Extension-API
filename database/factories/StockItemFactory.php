<?php

namespace Database\Factories;

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockItem>
 */
class StockItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $residentItems = ['Travesseiro', 'Edredom', 'Lençol', 'Toalha de banho'];
        $medicalItems = ['Agulha', 'Seringa', 'Luva descartável', 'Gaze', 'Álcool 70%'];

        $category = fake()->randomElement(StockItemCategoryEnum::cases());
        $name = $category === StockItemCategoryEnum::RESIDENT_SUPPLY
            ? fake()->randomElement($residentItems)
            : fake()->randomElement($medicalItems);

        return [
            'name' => $name,
            'category' => $category,
            'unit' => fake()->randomElement(StockItemUnitEnum::cases()),
            'current_quantity' => fake()->numberBetween(10, 200),
            'minimum_quantity' => fake()->numberBetween(1, 10),
            'requires_batch_control' => $category === StockItemCategoryEnum::MEDICAL_SUPPLY,
            'additional_information' => fake()->optional()->sentence(),
        ];
    }
}
