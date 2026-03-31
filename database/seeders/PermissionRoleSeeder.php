<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissionIds = Permission::query()->pluck('id')->all();

        if (empty($allPermissionIds)) {
            return;
        }

        Role::query()->each(function (Role $role) use ($allPermissionIds): void {
            if ($role->name === 'Administrador') {
                $role->permissions()->sync($allPermissionIds);

                return;
            }

            $role->permissions()->sync(
                Permission::query()
                    ->whereIn('name', ['listar', 'exibir'])
                    ->pluck('id')
                    ->all()
            );
        });
    }
}
