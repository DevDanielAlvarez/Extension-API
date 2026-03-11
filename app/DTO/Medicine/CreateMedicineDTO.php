<?php

namespace App\DTO\Medicine;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;

class CreateMedicineDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $content_quantity,
        public readonly ContentUnitEnum $content_unit,
        public readonly string $strength,
        public readonly bool $is_compounded,
        public readonly RouteOfAdministrationEnum $route_of_administration,
        public readonly ?string $additional_information,
    ) {
    }
}
