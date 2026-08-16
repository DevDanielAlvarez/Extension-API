<?php

namespace App\Filament\Resources\PatientMedicines\Pages;

use App\DTO\PatientMedicine\CreatePatientMedicineDTO;
use App\Filament\Resources\PatientMedicines\PatientMedicineResource;
use App\Services\PatientMedicine\PatientMedicineService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePatientMedicine extends CreateRecord
{
    protected static string $resource = PatientMedicineResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $createPatientMedicineDTO = new CreatePatientMedicineDTO(
            patient_id: $data['patient_id'],
            medicine_id: $data['medicine_id'],
            current_quantity: (int) ($data['current_quantity'] ?? 0),
            minimum_quantity: isset($data['minimum_quantity']) ? (int) $data['minimum_quantity'] : null,
        );

        return PatientMedicineService::create($createPatientMedicineDTO)->getRecord();
    }
}
