<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Administrador',
            'Enfermeiro',
            'Medico',
            'Cuidador',
            'Farmaceutico',
        ];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
    }
}
