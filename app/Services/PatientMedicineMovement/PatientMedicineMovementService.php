<?php

namespace App\Services\PatientMedicineMovement;

use App\DTO\PatientMedicineMovement\CreatePatientMedicineMovementDTO;
use App\Enums\StockMovementTypeEnum;
use App\Models\PatientMedicine;
use App\Models\PatientMedicineMovement;
use App\Services\PatientMedicine\PatientMedicineService;
use Illuminate\Database\Eloquent\Model;

class PatientMedicineMovementService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreatePatientMedicineMovementDTO $dtoToCreate): static
    {
        $patientMedicine = PatientMedicine::findOrFail($dtoToCreate->patient_medicine_id);

        $patientMedicineService = new PatientMedicineService($patientMedicine);

        // ADJUSTMENT corrects the balance to the counted value (physical inventory count);
        // the other types apply a signed delta on top of the current balance.
        match ($dtoToCreate->type) {
            StockMovementTypeEnum::IN => $patientMedicineService->adjustBalance($dtoToCreate->quantity),
            StockMovementTypeEnum::OUT => $patientMedicineService->adjustBalance(-$dtoToCreate->quantity),
            StockMovementTypeEnum::RETURNED => $patientMedicineService->adjustBalance($dtoToCreate->quantity),
            StockMovementTypeEnum::ADJUSTMENT => $patientMedicineService->setBalance($dtoToCreate->quantity),
        };

        return new self(PatientMedicineMovement::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $movement = PatientMedicineMovement::findOrFail($id);

        return new self($movement);
    }
}
