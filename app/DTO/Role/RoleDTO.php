<?php

namespace App\DTO\Role;

use Alvarez\ConcreteDto\AbstractDTO;

class RoleDTO extends AbstractDTO
{
    public function __construct(
        public string $name,
    ) {
    }
}
