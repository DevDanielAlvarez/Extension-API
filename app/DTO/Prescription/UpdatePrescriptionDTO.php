<?php

namespace App\DTO\Prescription;

use Alvarez\ConcreteDto\AbstractDTO;
use Carbon\Carbon;

class UpdatePrescriptionDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patient_id,
        public readonly string $medicine_id,
        public readonly Carbon $start_date,
        public readonly Carbon $end_date,
        public readonly string $instructions,
    ) {
    }

}