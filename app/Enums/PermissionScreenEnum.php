<?php

namespace App\Enums;

enum PermissionScreenEnum: string
{
    case MEDICINES_SCREEN = 'medicines_screen';
    case PATIENTS_SCREEN = 'patients_screen';
    case ROLES_SCREEN = 'roles_screen';
    case RESPONSIBLES_SCREEN = 'responsibles_screen';
    case PRESCRIPTIONS_SCREEN = 'prescriptions_screen';
}