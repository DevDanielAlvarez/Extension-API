<?php

namespace App\DTO\PatientMedicineMovement;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\StockMovementTypeEnum;
use Carbon\Carbon;

class CreatePatientMedicineMovementDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $patient_medicine_id,
        public readonly StockMovementTypeEnum $type,
        public readonly int $quantity,
        public readonly ?string $user_id,
        public readonly ?string $notes,
        public readonly Carbon $movement_date,
        public readonly ?string $medication_administration_id = null,
    ) {
    }
}
