<?php

namespace App\Filament\Resources\Responsibles\Pages;

use App\DTO\Responsible\CreateResponsibleDTO;
use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\Responsibles\ResponsibleResource;
use App\Services\Responsible\ResponsibleService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateResponsible extends CreateRecord
{
    protected static string $resource = ResponsibleResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $createResponsibleDTO = new CreateResponsibleDTO(
            name: $data['name'],
            document_type: ($data['document_type']),
            document_number: $data['document_number'],
            phone: $data['phone'] ?? null,
        );

        return ResponsibleService::create($createResponsibleDTO)->getRecord();
    }
}
