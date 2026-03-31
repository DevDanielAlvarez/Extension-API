<?php

namespace Database\Factories;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Gabriel', 'Beatriz', 'Thiago', 'Camila', 'Mateus', 'Aline', 'Felipe', 'Larissa', 'Vitor', 'Isabela'];
        $lastNames = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Costa', 'Pereira', 'Lima', 'Alves'];

        $documentType = fake()->randomElement([DocumentTypeEnum::CPF->value, DocumentTypeEnum::CNPJ->value]);

        return [
            'name' => fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames),
            'document_type' => $documentType,
            'document_number' => $documentType === DocumentTypeEnum::CPF->value
                ? fake()->unique()->numerify('###########')
                : fake()->unique()->numerify('##############'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
