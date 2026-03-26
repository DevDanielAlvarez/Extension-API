<?php

namespace Database\Seeders;

use App\Enums\PermissionScreenEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
            'forçar deletar',
            'forçar deletar em massa',
            'reordenar',
        ];

        foreach (PermissionScreenEnum::cases() as $screen) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $action,
                    'screen' => $screen->value,
                ]);
            }
        }
    }
}
