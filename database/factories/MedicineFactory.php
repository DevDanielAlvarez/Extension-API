<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'content_quantity' => fake()->numberBetween(1, 1000),
            'content_unit' => fake()->randomElement(['MG', 'MCG', 'G', 'ML', 'IU', 'UNIT']),
            'strength' => fake()->randomElement(['100mg', '250mg', '500mg', '1g', '10mg/ml']),
            'is_compounded' => fake()->boolean(),
            'route_of_administration' => fake()->randomElement(['ORAL', 'SUBLINGUAL', 'TOPICAL', 'INHALATION', 'INTRAVENOUS', 'INTRAMUSCULAR', 'SUBCUTANEOUS']),
            'additional_information' => fake()->optional()->sentence(),
        ];
    }
}
