<?php

namespace App\Filament\Resources\Patients\Pages;

use App\DTO\Patient\CreatePatientDTO;
use App\Filament\Resources\Patients\PatientResource;
use App\Services\Patient\PatientService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $createPatientDTO = new CreatePatientDTO(
            name: $data['name'],
            document_type: $data['document_type'],
            document_number: $data['document_number'],
            admission_date: Carbon::parse($data['admission_date']),
            birthday: Carbon::parse($data['birthday']),
            phone: $data['phone'],
            nursing_report: $data['nursing_report'],
        );
        return PatientService::create($createPatientDTO)->getRecord();
    }
}
