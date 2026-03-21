<?php

namespace App\Filament\Resources\Prescriptions\Pages;

use App\DTO\Prescription\CreatePrescriptionDTO;
use App\Filament\Resources\Prescriptions\PrescriptionResource;
use App\Services\Prescription\PrescriptionService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreatePrescription extends CreateRecord
{
    protected static string $resource = PrescriptionResource::class;

    public function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Create a DTO to pass the data to the service layer
        $dtoToCreatePrescription = new CreatePrescriptionDTO(
            patient_id: $data['patient_id'],
            medicine_id: $data['medicine_id'],
            start_date: Carbon::parse($data['start_date']),
            end_date: Carbon::parse($data['end_date']) ?? null,
            instructions: $data['instructions'] ?? null,
        );
        // create a prescription using the service layer
        return PrescriptionService::create($dtoToCreatePrescription)->getRecord();
    }
}
