<?php

namespace App\DTO\Role;

use Alvarez\ConcreteDto\AbstractDTO;

class CreateRoleDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
