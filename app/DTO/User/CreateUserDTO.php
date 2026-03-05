<?php

namespace App\DTO\User;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\DocumentTypeEnum;

class CreateUserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly DocumentTypeEnum $documentType,
        public readonly string $documentNumber,
        public readonly string $password,
    ) {}
}
