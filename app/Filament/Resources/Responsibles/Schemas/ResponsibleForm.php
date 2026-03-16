<?php

namespace App\Filament\Resources\Responsibles\Schemas;

use App\Enums\DocumentTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResponsibleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do responsável')
                    ->columnSpanFull()
                    ->description('Informações de identificação e contato do responsável.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->placeholder('Ex.: João Pereira')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('document_type')
                            ->translateLabel()
                            ->options(DocumentTypeEnum::class)
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('document_number')
                            ->translateLabel()
                            ->placeholder('Somente números')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->translateLabel()
                            ->tel()
                            ->columnSpanFull()
                            ->placeholder('Ex.: (11) 99999-9999')
                            ->maxLength(30),
                    ]),
            ]);
    }
}
