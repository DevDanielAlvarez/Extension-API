<?php

namespace Database\Seeders;

use App\Enums\PermissionScreenEnum;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'listar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'exibir', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'criar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'atualizar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'deletar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'deletar em massa', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'restaurar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'restaurar em massa', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'forçar deletar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'forçar deletar em massa', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'reordenar', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }
    }
}
