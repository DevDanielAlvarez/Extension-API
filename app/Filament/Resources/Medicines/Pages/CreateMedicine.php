<?php

namespace App\Filament\Resources\Medicines\Pages;

use App\DTO\Medicine\CreateMedicineDTO;
use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use App\Filament\Resources\Medicines\MedicineResource;
use App\Services\Medicine\MedicineService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMedicine extends CreateRecord
{
    protected static string $resource = MedicineResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $createMedicineDTO = new CreateMedicineDTO(
            name: $data['name'],
            content_quantity: $data['content_quantity'],
            content_unit: $data['content_unit'],
            strength: $data['strength'],
            is_compounded: $data['is_compounded'] ?? false,
            route_of_administration: $data['route_of_administration'],
            additional_information: $data['additional_information'] ?? null,
        );

        return MedicineService::create($createMedicineDTO)->getRecord();
    }
}
