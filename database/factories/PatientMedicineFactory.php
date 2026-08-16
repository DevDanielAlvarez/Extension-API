<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientMedicine>
 */
class PatientMedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'medicine_id' => Medicine::factory(),
            'current_quantity' => fake()->numberBetween(0, 60),
            'minimum_quantity' => fake()->numberBetween(2, 10),
        ];
    }
}
