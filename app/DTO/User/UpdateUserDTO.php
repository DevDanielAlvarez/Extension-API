<?php

namespace App\DTO\User;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\DocumentTypeEnum;

class UpdateUserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly DocumentTypeEnum $document_type,
        public readonly string $document_number,
        public readonly string $password,
    ) {
    }
}
