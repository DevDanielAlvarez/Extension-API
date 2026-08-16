<?php

namespace App\DTO\PatientMedicine;

use Alvarez\ConcreteDto\AbstractDTO;

/**
 * patient_id/medicine_id and current_quantity are intentionally absent:
 * identity does not change after creation, and balance only changes
 * through PatientMedicineMovement entries.
 */
class UpdatePatientMedicineDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?int $minimum_quantity,
    ) {
    }
}
