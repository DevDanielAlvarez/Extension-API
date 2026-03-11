<?php

namespace App\DTO\Medicine;

use Alvarez\ConcreteDto\AbstractDTO;

class MedicineDTO extends AbstractDTO
{
    public function __construct(
        public string $name,
        public int $contentQuantity,
        public string $contentUnit,
        public string $strength,
        public bool $isCompounded,
        public string $routeOfAdministration,
        public ?string $additionalInformation,
    ) {
    }
}
