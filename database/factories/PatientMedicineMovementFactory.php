<?php

namespace Database\Factories;

use App\Enums\StockMovementTypeEnum;
use App\Models\PatientMedicine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientMedicineMovement>
 */
class PatientMedicineMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_medicine_id' => PatientMedicine::factory(),
            'type' => StockMovementTypeEnum::IN,
            'quantity' => fake()->numberBetween(1, 30),
            'medication_administration_id' => null,
            'user_id' => User::factory(),
            'notes' => null,
            'movement_date' => now(),
        ];
    }
}
