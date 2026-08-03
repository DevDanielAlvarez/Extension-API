<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['document_type'] = DocumentTypeEnum::CPF->value;

        return $data;
    }
}
