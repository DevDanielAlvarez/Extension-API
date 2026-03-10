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
        public readonly null|string $password,
    ) {
    }

    public function toArray(): array
    {
        if ($this->password !== null) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'password' => $this->password,
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
        ];
    }
}
