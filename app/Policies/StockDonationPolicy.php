<?php

namespace App\Policies;

use App\Enums\PermissionScreenEnum;
use App\Models\User;
use App\Policies\Traits\CheckPermissionTrait;

class StockDonationPolicy
{
    use CheckPermissionTrait;

    public string $screen = PermissionScreenEnum::STOCK_SCREEN->value;

    public function viewAny(User $user): bool
    {
        return $this->check($user, 'listar', $this->screen);
    }

    public function view(User $user): bool
    {
        return $this->check($user, 'exibir', $this->screen);
    }

    public function update(User $user): bool
    {
        return $this->check($user, 'atualizar', $this->screen);
    }

    public function delete(User $user): bool
    {
        return $this->check($user, 'deletar', $this->screen);
    }
}
