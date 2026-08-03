<?php

namespace Database\Factories;

use App\Models\PrescriptionSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationAdministration>
 */
class MedicationAdministrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prescription_schedule_id' => PrescriptionSchedule::inRandomOrder()->first()->id ?? PrescriptionSchedule::factory()->create()->id,
            'scheduled_date' => today(),
            'applied_at' => null,
            'applied_by_user_id' => null,
        ];
    }
}
