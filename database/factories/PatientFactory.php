<?php

namespace Database\Factories;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Joao', 'Maria', 'Ana', 'Pedro', 'Lucas', 'Julia', 'Paulo', 'Carla', 'Rafael', 'Fernanda'];
        $lastNames = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Pereira', 'Costa', 'Rodrigues', 'Almeida'];

        $admissionDate = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'name' => fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames),
            'document_type' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->unique()->numerify('###########'),
            'admission_date' => $admissionDate,
            'birthday' => fake()->dateTimeBetween('-95 years', '-1 years'),
            'phone' => fake()->optional()->numerify('(##) 9####-####'),
            'nursing_report' => [
                'observacao' => fake()->randomElement([
                    'Paciente colaborativo durante os cuidados.',
                    'Refere dor leve no periodo da tarde.',
                    'Sinais vitais estaveis na ultima avaliacao.',
                ]),
            ],
        ];
    }
}
