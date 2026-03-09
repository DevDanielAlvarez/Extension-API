<?php

namespace App\DTO\User;

use Alvarez\ConcreteDto\AbstractDTO;

class UserDTO extends AbstractDTO
{
    public function __construct(
        public string $name,
        public string $documentType,
        public string $documentNumber,
        public string $password
    ) {}
}
