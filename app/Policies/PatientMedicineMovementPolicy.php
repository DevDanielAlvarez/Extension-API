<?php

namespace App\Policies;

use App\Enums\PermissionScreenEnum;
use App\Models\User;
use App\Policies\Traits\CheckPermissionTrait;

class PatientMedicineMovementPolicy
{
    use CheckPermissionTrait;

    public string $screen = PermissionScreenEnum::MEDICINES_SCREEN->value;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'listar', $this->screen);
    }

    public function view(User $user): bool
    {
        return $this->check($user, 'exibir', $this->screen);
    }

    public function create(User $user): bool
    {
        return $this->check($user, 'criar', $this->screen);
    }
}
