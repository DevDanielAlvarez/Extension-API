<?php

namespace Database\Factories;

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
        return [
            'name' => fake()->name(),
            'document_type' => fake()->randomElement(['CPF', 'CNPJ']),
            'document_number' => fake()->numerify('###########'), // 11 digits for CPF
            'admission_date' => fake()->date(),
            'birthday' => fake()->date(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
