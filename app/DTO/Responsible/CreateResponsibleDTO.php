<?php

namespace App\DTO\Responsible;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\DocumentTypeEnum;

class CreateResponsibleDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly DocumentTypeEnum $document_type,
        public readonly string $document_number,
        public readonly ?string $phone,
    ) {
    }
}
