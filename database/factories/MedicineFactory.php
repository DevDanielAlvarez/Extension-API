<?php

namespace Database\Factories;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medicines = [
            'Dipirona',
            'Paracetamol',
            'Ibuprofeno',
            'Amoxicilina',
            'Azitromicina',
            'Losartana',
            'Metformina',
            'Omeprazol',
            'Insulina NPH',
            'Insulina Regular',
            'Rivaroxabana',
            'Cefalexina',
            'Hidralazina',
            'Hidroclorotiazida',
            'Sertralina',
            'Fluoxetina',
        ];

        $route = fake()->randomElement(RouteOfAdministrationEnum::cases())->value;
        $unit = fake()->randomElement(ContentUnitEnum::cases())->value;

        return [
            'name' => fake()->randomElement($medicines),
            'content_quantity' => fake()->numberBetween(1, 1000),
            'content_unit' => $unit,
            'strength' => fake()->randomElement(['5 mg', '10 mg', '20 mg', '100 mg', '250 mg', '500 mg', '1 g', '10 mg/ml']),
            'is_compounded' => fake()->boolean(),
            'route_of_administration' => $route,
            'additional_information' => fake()->optional()->randomElement([
                'Administrar após refeição.',
                'Manter sob refrigeração após abertura.',
                'Uso contínuo conforme prescrição médica.',
                'Suspender em caso de reação alérgica.',
            ]),
        ];
    }
}
