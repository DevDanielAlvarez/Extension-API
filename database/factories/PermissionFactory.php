<?php

namespace Database\Factories;

use App\Enums\PermissionScreenEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = [
            'listar',
            'exibir',
            'criar',
            'atualizar',
            'deletar',
            'deletar em massa',
            'restaurar',
            'restaurar em massa',
            'forcar deletar',
            'forcar deletar em massa',
            'reordenar',
        ];

        return [
            'name' => fake()->randomElement($actions),
            'screen' => fake()->randomElement(PermissionScreenEnum::cases())->value,
        ];
    }
}
