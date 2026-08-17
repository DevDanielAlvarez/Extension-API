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

        $route = fake()->randomElement(RouteOfAdministrationEnum::cases());

        // content_quantity/content_unit describe the total content of the package
        // (e.g. "30 comprimidos por caixa", "100 ml por frasco"), not a stock
        // balance. strength describes the dose concentration (e.g. "500 mg" per
        // comprimido, "10 mg/ml"). Both must match the route/form of the medicine
        // to read as a coherent catalog entry.
        [$unit, $quantities, $strengths] = fake()->randomElement($this->archetypesFor($route));

        return [
            'name' => fake()->randomElement($medicines),
            'content_quantity' => fake()->randomElement($quantities),
            'content_unit' => $unit->value,
            'strength' => fake()->randomElement($strengths),
            'is_compounded' => fake()->boolean(20),
            'route_of_administration' => $route->value,
            'additional_information' => fake()->optional()->randomElement([
                'Administrar após refeição.',
                'Manter sob refrigeração após abertura.',
                'Uso contínuo conforme prescrição médica.',
                'Suspender em caso de reação alérgica.',
            ]),
        ];
    }

    /**
     * Plausible (content_unit, content_quantity options, strength options)
     * combinations for each route of administration.
     *
     * @return array<int, array{0: ContentUnitEnum, 1: array<int>, 2: array<string>}>
     */
    protected function archetypesFor(RouteOfAdministrationEnum $route): array
    {
        return match ($route) {
            RouteOfAdministrationEnum::ORAL => [
                [ContentUnitEnum::UNIT, [10, 20, 30, 60, 90, 100], ['5 mg', '10 mg', '20 mg', '25 mg', '50 mg', '100 mg', '250 mg', '500 mg', '850 mg', '1 g']],
                [ContentUnitEnum::ML, [60, 100, 120, 150, 200], ['5 mg/ml', '10 mg/ml', '20 mg/ml', '40 mg/ml']],
            ],
            RouteOfAdministrationEnum::SUBLINGUAL => [
                [ContentUnitEnum::UNIT, [10, 20, 30], ['0.25 mg', '0.5 mg', '5 mg', '10 mg']],
            ],
            RouteOfAdministrationEnum::TOPICAL => [
                [ContentUnitEnum::G, [15, 20, 30, 50, 60], ['0.5%', '1%', '2%', '5%']],
                [ContentUnitEnum::ML, [30, 60, 100], ['1%', '2%']],
            ],
            RouteOfAdministrationEnum::INHALATION => [
                [ContentUnitEnum::UNIT, [60, 100, 120, 200], ['50 mcg/dose', '100 mcg/dose', '200 mcg/dose', '250 mcg/dose']],
            ],
            RouteOfAdministrationEnum::INTRAVENOUS, RouteOfAdministrationEnum::INTRAMUSCULAR => [
                [ContentUnitEnum::ML, [1, 2, 5, 10, 20], ['10 mg/ml', '25 mg/ml', '50 mg/ml', '100 mg/ml']],
            ],
            RouteOfAdministrationEnum::SUBCUTANEOUS => [
                [ContentUnitEnum::IU, [300, 1000], ['100 UI/ml']],
                [ContentUnitEnum::ML, [1, 2, 3], ['20 mg/ml', '40 mg/ml']],
            ],
        };
    }
}
