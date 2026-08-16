<?php

namespace App\DTO\PatientMedicine;

use Alvarez\ConcreteDto\AbstractDTO;

class CreatePatientMedicineDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $patient_id,
        public readonly string $medicine_id,
        public readonly int $current_quantity,
        public readonly ?int $minimum_quantity,
    ) {
    }
}
