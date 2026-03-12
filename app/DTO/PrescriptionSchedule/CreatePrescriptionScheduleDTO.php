<?php

namespace App\DTO\PrescriptionSchedule;

use Alvarez\ConcreteDto\AbstractDTO;

class CreatePrescriptionScheduleDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $prescription_id,
        public readonly int $day_of_week,
        public readonly string $time,
        public readonly int $quantity,
    ) {
    }
}
