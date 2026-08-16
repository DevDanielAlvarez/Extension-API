<?php

namespace App\Filament\Resources\PatientMedicines\Pages;

use App\Filament\Resources\PatientMedicines\PatientMedicineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientMedicines extends ListRecords
{
    protected static string $resource = PatientMedicineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
