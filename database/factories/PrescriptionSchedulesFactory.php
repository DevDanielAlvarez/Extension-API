<?php

namespace Database\Factories;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrescriptionSchedules>
 */
class PrescriptionSchedulesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::inRandomOrder()->first()->id ?? Prescription::factory()->create()->id,
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'time' => $this->faker->time('H:i'),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
