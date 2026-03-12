<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id ?? Patient::factory()->create()->id,
            'medicine_id' => Medicine::inRandomOrder()->first()->id ?? Medicine::factory()->create()->id,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'instructions' => $this->faker->text(200),
        ];
    }
}
