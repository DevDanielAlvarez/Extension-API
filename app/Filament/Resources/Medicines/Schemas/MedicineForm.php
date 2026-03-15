<?php

namespace App\Filament\Resources\Medicines\Schemas;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MedicineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do medicamento')
                    ->columnSpanFull()
                    ->description('Defina os dados principais de apresentação e administração.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->placeholder('Ex.: Dipirona')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('content_quantity')
                            ->translateLabel()
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Ex.: 20'),
                        Select::make('content_unit')
                            ->translateLabel()
                            ->options(ContentUnitEnum::class)
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('strength')
                            ->translateLabel()
                            ->required()
                            ->placeholder('Ex.: 500mg'),
                        Select::make('route_of_administration')
                            ->translateLabel()
                            ->options(RouteOfAdministrationEnum::class)
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('is_compounded')
                            ->translateLabel()
                            ->inline(false)
                            ->onColor('warning')
                            ->offColor('gray')
                            ->helperText('Marque quando for um medicamento manipulado.')
                            ->default(false)
                            ->columnSpanFull(),
                        Textarea::make('additional_information')
                            ->translateLabel()
                            ->rows(4)
                            ->placeholder('Informações adicionais para uso e armazenamento...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
