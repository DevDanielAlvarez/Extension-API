<?php

namespace Database\Seeders;

use App\Enums\PermissionScreenEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'create_medicine', 'screen' => PermissionScreenEnum::MEDICINES_SCREEN->value],
            ['name' => 'read_medicine', 'screen' => PermissionScreenEnum::MEDICINES_SCREEN->value],
            ['name' => 'update_medicine', 'screen' => PermissionScreenEnum::MEDICINES_SCREEN->value],
            ['name' => 'delete_medicine', 'screen' => PermissionScreenEnum::MEDICINES_SCREEN->value],

            ['name' => 'create_patient', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'read_patient', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'update_patient', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],
            ['name' => 'delete_patient', 'screen' => PermissionScreenEnum::PATIENTS_SCREEN->value],

            ['name' => 'create_role', 'screen' => PermissionScreenEnum::ROLES_SCREEN->value],
            ['name' => 'read_role', 'screen' => PermissionScreenEnum::ROLES_SCREEN->value],
            ['name' => 'update_role', 'screen' => PermissionScreenEnum::ROLES_SCREEN->value],
            ['name' => 'delete_role', 'screen' => PermissionScreenEnum::ROLES_SCREEN->value],

            ['name' => 'create_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],
            ['name' => 'read_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],
            ['name' => 'update_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],
            ['name' => 'delete_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],

            ['name' => 'create_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'read_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'update_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'delete_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'read_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],
            ['name' => 'update_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],
            ['name' => 'delete_responsible', 'screen' => PermissionScreenEnum::RESPONSIBLES_SCREEN->value],

            ['name' => 'create_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'read_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'update_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
            ['name' => 'delete_prescription', 'screen' => PermissionScreenEnum::PRESCRIPTIONS_SCREEN->value],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }
    }
}
