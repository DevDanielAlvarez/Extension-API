<?php

namespace App\Filament\Resources\Responsibles\Schemas;

use App\Enums\DocumentTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResponsibleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->translateLabel()
                    ->required(),
                Select::make('document_type')
                    ->translateLabel()
                    ->options(DocumentTypeEnum::class)
                    ->required(),
                TextInput::make('document_number')
                    ->translateLabel()
                    ->required(),
                TextInput::make('phone')
                    ->translateLabel()
                    ->tel(),
            ]);
    }
}
