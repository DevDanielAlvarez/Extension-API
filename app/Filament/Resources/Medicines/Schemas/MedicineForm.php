<?php

namespace App\Filament\Resources\Medicines\Schemas;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MedicineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->translateLabel()
                    ->required(),
                TextInput::make('content_quantity')
                    ->translateLabel()
                    ->required()
                    ->numeric(),
                Select::make('content_unit')
                    ->translateLabel()
                    ->options(ContentUnitEnum::class)
                    ->required(),
                TextInput::make('strength')
                    ->translateLabel()
                    ->required(),
                Toggle::make('is_compounded')
                    ->translateLabel()
                    ->required(),
                Select::make('route_of_administration')
                    ->translateLabel()
                    ->options(RouteOfAdministrationEnum::class)
                    ->required(),
                Textarea::make('additional_information')
                    ->translateLabel()
                    ->columnSpanFull(),
            ]);
    }
}
