<?php

namespace Database\Factories;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrescriptionSchedule>
 */
class PrescriptionScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commonTimes = ['06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];

        return [
            'prescription_id' => Prescription::inRandomOrder()->first()->id ?? Prescription::factory()->create()->id,
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'time' => $this->faker->randomElement($commonTimes),
            'quantity' => $this->faker->numberBetween(1, 3),
        ];
    }
}
