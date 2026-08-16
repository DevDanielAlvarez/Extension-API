<?php

namespace App\Filament\Resources\PatientMedicines\Pages;

use App\DTO\PatientMedicine\UpdatePatientMedicineDTO;
use App\Filament\Resources\PatientMedicines\PatientMedicineResource;
use App\Services\PatientMedicine\PatientMedicineService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPatientMedicine extends EditRecord
{
    protected static string $resource = PatientMedicineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $updatePatientMedicineDTO = new UpdatePatientMedicineDTO(
            id: $record->id,
            minimum_quantity: isset($data['minimum_quantity']) ? (int) $data['minimum_quantity'] : null,
        );

        return PatientMedicineService::find($record->id)->update($updatePatientMedicineDTO)->getRecord();
    }
}
