<?php

namespace Database\Factories;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Responsible>
 */
class ResponsibleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Marcos', 'Patricia', 'Renata', 'Carlos', 'Bruna', 'Ricardo', 'Eliane', 'Leandro', 'Tatiane'];
        $lastNames = ['Silva', 'Souza', 'Ferreira', 'Araujo', 'Lima', 'Gomes', 'Barbosa'];

        $documentType = fake()->randomElement([DocumentTypeEnum::CPF->value, DocumentTypeEnum::CNPJ->value]);

        return [
            'name' => fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames),
            'document_type' => $documentType,
            'document_number' => $documentType === DocumentTypeEnum::CPF->value
                ? fake()->unique()->numerify('###########')
                : fake()->unique()->numerify('##############'),
            'phone' => fake()->optional()->numerify('(##) 9####-####'),
        ];
    }
}
