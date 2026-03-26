<?php

namespace App\Policies;

use App\Enums\PermissionScreenEnum;
use App\Models\User;
use App\Policies\Traits\CheckPermissionTrait;

class MedicinePolicy
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

    public function update(User $user): bool
    {
        return $this->check($user, 'atualizar', $this->screen);
    }

    public function delete(User $user): bool
    {
        return $this->check($user, 'deletar', $this->screen);
    }

    public function deleteAny(User $user): bool
    {
        return $this->check($user, 'deletar em massa', $this->screen);
    }

    public function restore(User $user): bool
    {
        return $this->check($user, 'restaurar', $this->screen);
    }

    public function restoreAny(User $user): bool
    {
        return $this->check($user, 'restaurar em massa', $this->screen);
    }

    public function forceDelete(User $user): bool
    {
        return $this->check($user, 'forçar deletar', $this->screen);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->check($user, 'forçar deletar em massa', $this->screen);
    }

    public function reorder(User $user): bool
    {
        return $this->check($user, 'reordenar', $this->screen);
    }
}
