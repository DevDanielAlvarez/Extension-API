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
        $documentType = fake()->randomElement([DocumentTypeEnum::CPF->value, DocumentTypeEnum::CNPJ->value]);

        return [
            'name' => fake()->name(),
            'document_type' => $documentType,
            'document_number' => $documentType === DocumentTypeEnum::CPF->value
                ? fake()->numerify('###########')
                : fake()->numerify('##############'),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
