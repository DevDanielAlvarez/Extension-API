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
        $startDate = fake()->dateTimeBetween('-30 days', 'now');
        $hasEndDate = fake()->boolean(70);

        return [
            'patient_id' => Patient::inRandomOrder()->first()->id ?? Patient::factory()->create()->id,
            'medicine_id' => Medicine::inRandomOrder()->first()->id ?? Medicine::factory()->create()->id,
            'start_date' => $startDate,
            'end_date' => $hasEndDate ? fake()->dateTimeBetween($startDate, '+45 days') : null,
            'instructions' => fake()->randomElement([
                'Administrar apos cafe da manha e jantar.',
                'Aplicar conforme horario e observar resposta clinica.',
                'Nao interromper sem orientacao medica.',
                'Administrar com monitoramento de sinais vitais.',
            ]),
        ];
    }
}
