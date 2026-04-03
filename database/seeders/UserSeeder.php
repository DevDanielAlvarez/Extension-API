<?php

namespace Database\Seeders;

use App\Enums\DocumentTypeEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            [
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '123',
            ],
            [
                'name' => 'Administrador do Sistema',
                'password' => Hash::make('123'),
                'is_adm' => true,
            ]
        );

        $operador = User::query()->firstOrCreate(
            [
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '1234',
            ],
            [
                'name' => 'Operador Clinico',
                'password' => Hash::make('1234'),
                'is_adm' => false,
            ]
        );

        $users = User::factory()->count(18)->create();

        $roles = Role::query()->pluck('id');

        $users->each(function (User $user) use ($roles): void {
            if ($roles->isEmpty()) {
                return;
            }

            $user->roles()->syncWithoutDetaching(
                $roles->random(rand(1, min(2, $roles->count())))->values()->all()
            );
        });

        $defaultRoleIds = Role::query()->whereIn('name', ['Enfermeiro', 'Medico'])->pluck('id')->all();

        if (! empty($defaultRoleIds)) {
            $operador->roles()->syncWithoutDetaching($defaultRoleIds);
        }

        $admin->roles()->syncWithoutDetaching(Role::query()->pluck('id')->all());
    }
}
