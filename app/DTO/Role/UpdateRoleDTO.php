<?php

namespace App\DTO\Role;

use Alvarez\ConcreteDto\AbstractDTO;

class UpdateRoleDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
