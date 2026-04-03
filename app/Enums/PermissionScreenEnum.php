<?php

namespace App\Enums;

enum PermissionScreenEnum: string
{
    case MEDICINES_SCREEN = 'medicines_screen';
    case USERS_SCREEN = 'users_screen';
    case PATIENTS_SCREEN = 'patients_screen';
    case ROLES_SCREEN = 'roles_screen';
    case RESPONSIBLES_SCREEN = 'responsibles_screen';
    case PRESCRIPTIONS_SCREEN = 'prescriptions_screen';

    case GIVE_PERMISSIONS_TO_ROLES_SCREEN = 'give_permissions_to_roles_screen';
    
    public function label(): string
    {
        return match ($this) {
            self::MEDICINES_SCREEN => __('Medicines Screens'),
            self::USERS_SCREEN => __('Users Screens'),
            self::PATIENTS_SCREEN => __('Patients Screens'),
            self::ROLES_SCREEN => __('Roles Screens'),
            self::RESPONSIBLES_SCREEN => __('Responsibles Screens'),
            self::PRESCRIPTIONS_SCREEN => __('Prescriptions Screens'),
            self::GIVE_PERMISSIONS_TO_ROLES_SCREEN => __('Give Permissions To Roles Screen'),
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $screen) => [$screen->value => $screen->label()])
            ->toArray();
    }
}